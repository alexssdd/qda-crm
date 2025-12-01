<?php

namespace app\core\hash;

/**
 * Short hash
 */
class HashCrypto
{
    const METHOD = 'AES-256-CBC';
    const KEY = 'i(Y';
    const IV = 'aX,@a&,';

    /**
     * @param $text
     * @return string
     */
    public function make($text): string
    {
        // Variables
        $key = hash('sha256', self::KEY);
        $iv = substr(hash('sha256', self::IV), 0, 16);

        return base64_encode(openssl_encrypt($text, self::METHOD, $key, 0, $iv));
    }

    /**
     * @param $text
     * @return string
     */
    public function extract($text): string
    {
        // Variables
        $key = hash('sha256', self::KEY);
        $iv = substr(hash('sha256', self::IV), 0, 16);

        return openssl_decrypt(base64_decode($text), self::METHOD, $key, 0, $iv);
    }
}