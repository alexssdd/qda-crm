<?php

namespace app\core\helpers;

use InvalidArgumentException;
use RuntimeException;

class CryptoHelper
{
    private const CIPHER = 'aes-256-cbc';

    /**
     * Шифрует произвольное значение AES-256-CBC. Не-строки пакуются в JSON
     * перед шифрованием — `decrypt` распакует обратно. Строки/числа/bool
     * шифруются как строка (для них JSON-обёртка не нужна).
     */
    public static function encrypt(mixed $data, string $key): string
    {
        if (!is_string($data)) {
            $encoded = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($encoded === false) {
                throw new InvalidArgumentException('CryptoHelper::encrypt: failed to JSON-encode payload: ' . json_last_error_msg());
            }
            $data = $encoded;
        }

        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encrypted = openssl_encrypt($data, self::CIPHER, $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    /**
     * Расшифровывает значение. Если plaintext парсится JSON'ом в массив/объект
     * — возвращает массив (для секретов вида `{"id":"a","secret":"b"}`).
     * Простые строки/числа возвращаются как строка (без JSON-обёртки они
     * и шифровались).
     */
    public static function decrypt(string $encrypted, string $key): mixed
    {
        $decoded = base64_decode($encrypted);
        $ivLength = openssl_cipher_iv_length(self::CIPHER);
        $iv = substr($decoded, 0, $ivLength);
        $data = substr($decoded, $ivLength);
        $plain = openssl_decrypt($data, self::CIPHER, $key, 0, $iv);
        if ($plain === false) {
            throw new RuntimeException('CryptoHelper::decrypt: openssl_decrypt failed (wrong key or corrupted payload)');
        }

        $unpacked = json_decode($plain, true);
        if (is_array($unpacked)) {
            return $unpacked;
        }

        return $plain;
    }
}
