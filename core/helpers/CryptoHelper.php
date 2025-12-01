<?php

namespace app\core\helpers;

class CryptoHelper
{
    private const CIPHER = 'aes-256-cbc';

    public static function encrypt(string $data, string $key): string
    {
        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encrypted = openssl_encrypt($data, self::CIPHER, $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    public static function decrypt(string $encrypted, string $key): string
    {
        $decoded = base64_decode($encrypted);
        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        $iv = substr($decoded, 0, $ivLength);
        $data = substr($decoded, $ivLength);
        return openssl_decrypt($data, self::CIPHER, $key, 0, $iv);
    }
}