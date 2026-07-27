<?php

namespace app\modules\auth\services;

use RuntimeException;
use Throwable;
use Yii;
use app\services\ConfigService;
use app\modules\auth\providers\OtpInterface;
use app\modules\auth\providers\MessaggioOtpProvider;
use app\modules\auth\providers\KazInfoTehOtpProvider;

final class OtpDeliveryService
{
    private const CONFIG_GROUP = 'otp';
    private const DEFAULT_POLICY = ['messaggio', 'kazinfoteh'];
    private const SMS_PROVIDER = 'kazinfoteh';

    /** @var array<string, OtpInterface> */
    private array $providers;

    public function __construct(
        private ConfigService $config,
        MessaggioOtpProvider $messaggio,
        KazInfoTehOtpProvider $kazInfoTeh,
    ) {
        $this->providers = [
            'messaggio' => $messaggio,
            'kazinfoteh' => $kazInfoTeh,
        ];
    }

    public function generateOtp(): string
    {
        return (string) random_int(100000, 999999);
    }

    public function sendOtp(
        string $to,
        string $code,
        string $language,
        bool $isRetry
    ): void {
        if ($isRetry) {
            foreach ($this->policy() as $providerName) {
                if ($providerName === self::SMS_PROVIDER) {
                    $this->provider($providerName)->sendOtp($to, $code, $language);
                    return;
                }
            }

            throw new RuntimeException('OTP policy does not contain an SMS provider');
        }

        $lastError = null;
        foreach ($this->policy() as $providerName) {
            try {
                $this->provider($providerName)->sendOtp($to, $code, $language);
                return;
            } catch (Throwable $e) {
                $lastError = $e;
                Yii::warning([
                    'event' => 'crm_otp_provider_failed',
                    'provider' => $providerName,
                    'exception' => get_class($e),
                ], 'auth.otp');
            }
        }

        throw new RuntimeException('All OTP providers failed', 0, $lastError);
    }

    /**
     * @return string[]
     */
    private function policy(): array
    {
        $policy = $this->config->get(
            self::CONFIG_GROUP,
            'policy',
            self::DEFAULT_POLICY
        );

        if (!is_array($policy) || $policy === []) {
            return self::DEFAULT_POLICY;
        }

        return array_values(array_filter(
            $policy,
            fn ($provider) => is_string($provider) && isset($this->providers[$provider])
        ));
    }

    private function provider(string $name): OtpInterface
    {
        if (!isset($this->providers[$name])) {
            throw new RuntimeException("Unknown OTP provider: {$name}");
        }

        return $this->providers[$name];
    }
}
