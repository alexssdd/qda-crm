<?php
namespace app\modules\auth\providers;

interface OtpInterface
{
    public function generateOtp(): string;

    public function sendOtp(string $to, string $code, string $language);
}