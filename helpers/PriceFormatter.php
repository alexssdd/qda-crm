<?php

namespace app\helpers;

use Yii;
use DomainException;

final class PriceFormatter
{
    private const THOUSAND = 1_000;
    private const MILLION  = 1_000_000;
    private const BILLION  = 1_000_000_000;

    public static function short(
        float  $amount,
        string $countryCode,
        int    $decimals = 1,
        int    $threshold = self::MILLION
    ): string {
        if ($countryCode === '') {
            throw new DomainException('$countryCode cannot be empty');
        }

        $amount = (float)$amount;

        if ($amount >= self::BILLION && $threshold <= self::BILLION) {
            $num    = round($amount / self::BILLION, $decimals);
            $suffix = Yii::t('app', 'price.billion');
        } elseif ($amount >= self::MILLION && $threshold <= self::MILLION) {
            $num    = round($amount / self::MILLION, $decimals);
            $suffix = Yii::t('app', 'price.million');
        } elseif ($amount >= self::THOUSAND && $threshold <= self::THOUSAND) {
            $num    = round($amount / self::THOUSAND, $decimals);
            $suffix = Yii::t('app', 'price.thousand');
        } else {
            $num    = Yii::$app->formatter->asInteger($amount);
            $suffix = '';
        }

        $num = preg_replace('~\.0+$~', '', (string)$num);

        $symbol = CurrencyHelper::getSymbolByCountry($countryCode)
            ?? throw new DomainException("Unknown country code: $countryCode");

        $result = (string)$num;

        if ($suffix !== '') {
            $result .= ' ' . $suffix;
        }

        $result .= ' ' . $symbol;

        return $result;
    }
}