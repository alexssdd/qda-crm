<?php

namespace app\modules\auth\providers;

use RuntimeException;
use Throwable;
use app\services\ConfigService;
use yii\httpclient\Client;

final class KazInfoTehOtpProvider implements OtpInterface
{
    private const CONFIG_GROUP = 'kazinfoteh';
    private const DEFAULT_URL = 'https://so.kazinfoteh.org/api/sms/send';
    private const TIMEOUT_SECONDS = 10;
    private const APPROVED_SENDER_PREFIXES = ['7705', '7771', '7776', '7777'];

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
        $recipient = $this->toRecipient($to);
        $this->assertNotBlocked($recipient);

        $text = $this->message($code, $language);
        if (!$this->hasApprovedSender($recipient)) {
            $prefix = (string) $this->config->get(self::CONFIG_GROUP, 'text_prefix', '');
            if ($prefix !== '') {
                $text = $prefix . ' ' . $text;
            }
        }

        $body = [
            'from' => (string) $this->config->get(self::CONFIG_GROUP, 'sender', ''),
            'to' => $recipient,
            'text' => $text,
        ];

        try {
            $response = $this->getClient()
                ->post('', $body)
                ->setOptions(['timeout' => self::TIMEOUT_SECONDS])
                ->send();
        } catch (Throwable $e) {
            throw new RuntimeException(
                'KazInfoTeh HTTP request failed: ' . get_class($e),
                0,
                $e
            );
        }

        if (!$response->getIsOk()) {
            throw new RuntimeException(sprintf(
                'KazInfoTeh HTTP request failed with status %d',
                $response->getStatusCode()
            ));
        }

        $data = $response->getData();
        if (!is_array($data)) {
            throw new RuntimeException('KazInfoTeh returned an unexpected response');
        }

        if (($data['err'] ?? null) !== null) {
            throw new RuntimeException('KazInfoTeh rejected the SMS request');
        }
    }

    private function getClient(): Client
    {
        if ($this->client === null) {
            $token = (string) $this->config->getRequired(self::CONFIG_GROUP, 'api_token');
            $url = (string) $this->config->get(
                self::CONFIG_GROUP,
                'api_url',
                self::DEFAULT_URL
            );

            $this->client = new Client([
                'baseUrl' => $url,
                'requestConfig' => [
                    'format' => Client::FORMAT_JSON,
                    'headers' => [
                        'Authorization' => 'Basic ' . $token,
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

    private function toRecipient(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);
        if (strlen($digits) === 11 && str_starts_with($digits, '8')) {
            $digits = '7' . substr($digits, 1);
        }

        if (!preg_match('/^7\d{10}$/', $digits)) {
            throw new RuntimeException('KazInfoTeh requires a Kazakhstan phone number');
        }

        return $digits;
    }

    private function assertNotBlocked(string $recipient): void
    {
        $blocked = $this->config->get(self::CONFIG_GROUP, 'blocked_phone_prefixes', []);
        if (!is_array($blocked)) {
            return;
        }

        foreach ($blocked as $prefix) {
            if (is_string($prefix) && $prefix !== '' && str_starts_with($recipient, $prefix)) {
                throw new RuntimeException('KazInfoTeh recipient operator is not enabled');
            }
        }
    }

    private function hasApprovedSender(string $recipient): bool
    {
        foreach (self::APPROVED_SENDER_PREFIXES as $prefix) {
            if (str_starts_with($recipient, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function message(string $code, string $language): string
    {
        return match (strtolower(substr($language, 0, 2))) {
            'kk' => "{$code} — QDA CRM жүйесіне кіру коды. Код 2 минут жарамды.",
            'en' => "{$code} is your QDA CRM login code. It is valid for 2 minutes.",
            'uz' => "{$code} — QDA CRM tizimiga kirish kodi. Kod 2 daqiqa amal qiladi.",
            default => "{$code} — код входа в QDA CRM. Код действует 2 минуты.",
        };
    }
}
