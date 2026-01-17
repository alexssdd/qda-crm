<?php
namespace app\modules\order\helpers;

class PaymentHelper
{
    const METHOD_CASH = 10;
    const METHOD_KASPI_TRANSFER = 11;
    const METHOD_CARD = 12;
    const METHOD_BANK_TRANSFER = 13;

    public static function getMethods()
    {
        return [
            self::METHOD_CASH => 'Cash',
            self::METHOD_CARD => 'Card',
            self::METHOD_KASPI_TRANSFER => 'Kaspi transfer',
            self::METHOD_BANK_TRANSFER => 'Bank transfer',
        ];
    }
}