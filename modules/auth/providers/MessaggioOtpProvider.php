<?php

namespace app\modules\auth\providers;

use RuntimeException;
use Throwable;
use app\core\http\SensitiveHttpClient;
use app\services\ConfigService;
use yii\httpclient\Client;
use yii\httpclient\CurlTransport;

final class MessaggioOtpProvider implements OtpInterface
{
    private const CONFIG_GROUP = 'messaggio';
    private const TIMEOUT_SECONDS = 10;

    private ?Client $client = null;

    public function __construct(private ConfigService $config)
    {
    }

    public function generateOtp(): string
    {
        return (string) random_int(100000, 999999);
    }

    public function sendOtp(string $to, string $code, string $language): void
    {
        $phone = preg_replace('/\D+/', '', $to);
        if (!preg_match('/^\d{10,15}$/', $phone)) {
            throw new RuntimeException('Messaggio requires an international phone number');
        }

        $payload = [
            'recipients' => [['phone' => $phone]],
            'channels' => ['telegram-otp'],
            'telegram-otp' => [
                'from' => (string) $this->config->getRequired(
                    self::CONFIG_GROUP,
                    'sender_code'
                ),
                'content' => ['code' => $code],
            ],
        ];

        try {
            $response = $this->getClient()
                ->post('send', $payload)
                ->setOptions(['timeout' => self::TIMEOUT_SECONDS])
                ->send();
        } catch (Throwable $e) {
            throw new RuntimeException(
                'Messaggio HTTP request failed: ' . get_class($e),
                0,
                $e
            );
        }

        if (!$response->getIsOk()) {
            throw new RuntimeException(sprintf(
                'Messaggio HTTP request failed with status %d',
                $response->getStatusCode()
            ));
        }

        $data = $response->getData();
        if (
            !is_array($data)
            || !isset($data['accepted_at'])
            || empty($data['messages'])
        ) {
            throw new RuntimeException('Messaggio rejected the Telegram OTP request');
        }
    }

    private function getClient(): Client
    {
        if ($this->client === null) {
            $baseUrl = (string) $this->config->getRequired(
                self::CONFIG_GROUP,
                'api_url'
            );
            $apiLogin = (string) $this->config->getRequired(
                self::CONFIG_GROUP,
                'api_login'
            );

            $this->client = new SensitiveHttpClient([
                'baseUrl' => rtrim($baseUrl, '/') . '/',
                'transport' => CurlTransport::class,
                'requestConfig' => [
                    'format' => Client::FORMAT_JSON,
                    'headers' => [
                        'Messaggio-Login' => $apiLogin,
                        'Content-Type' => 'application/json',
                    ],
                ],
                'responseConfig' => [
                    'format' => Client::FORMAT_JSON,
                ],
            ]);
        }

        return $this->client;
    }
}
