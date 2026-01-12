<?php

namespace app\modules\auth\providers;

class FakeOtpProvider implements OtpInterface
{
    public function generateOtp(): string
    {
        return '777777';
    }

    public function sendOtp(string $to, string $code, string $language): void
    {
        return;
    }
}