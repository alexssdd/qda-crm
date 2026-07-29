<?php

namespace app\modules\location\helpers;

final class LocationHelper
{
    public static function getName(
        array|string|null $extraFields,
        string $fallback,
        string $language = 'ru'
    ): string {
        if (is_string($extraFields)) {
            $extraFields = json_decode($extraFields, true);
        }

        if (!is_array($extraFields)) {
            return $fallback;
        }

        $name = trim((string) ($extraFields['names'][$language] ?? ''));

        return $name !== '' ? $name : $fallback;
    }
}
