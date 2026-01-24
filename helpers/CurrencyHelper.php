<?php

namespace app\helpers;

class CurrencyHelper
{
    public static function getSymbolByCountry(string $countryCode): ?string
    {
        $data = [
            'kz' => '₸',
            'ru' => '₽',
            'us' => '$',
            'gb' => '£',
            'eu' => '€',
            'kg' => 'сом',
            'uz' => 'сўм',
            'tr' => '₺',
        ];

        return $data[$countryCode] ?? null;
    }
}