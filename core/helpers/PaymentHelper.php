<?php

namespace app\core\helpers;

use Yii;
use Exception;
use yii\helpers\ArrayHelper;

/**
 * Payment helper
 */
class PaymentHelper
{
    /** Methods */
    const METHOD_MIXED = 9;

    const PAYMENT_CASH = 10;
    const PAYMENT_AIRBA_PAY = 11;

    const PAYMENT_KASPI_INVOICE = 12;
    const PAYMENT_KASPI_SHOP = 13;
    const PAYMENT_KASPI_ONLINE = 19;

    const PAYMENT_HALYK_MARKET = 14;
    const PAYMENT_HALYK_ONLINE = 22;

    const PAYMENT_WOLT = 15;
    const PAYMENT_GLOVO = 16;
    const PAYMENT_YANDEX_EDA = 17;
    const PAYMENT_JUSAN = 18;

    const PAYMENT_BCK_ONLINE = 20;
    const METHOD_BANK_INVOICE = 21;
    const METHOD_EPS = 23; // Электронный сертификат
    const METHOD_OZON = 24;
    const METHOD_BONUS = 25;
    const METHOD_WB = 26;
    const METHOD_FORTE = 27;

    /** Providers */
    const PROVIDER_HALYK_CODE = 'halyk';
    const PROVIDER_KASPI_CODE = 'kaspi';
    const PROVIDER_EPS_CODE = 'eps';
    const PROVIDER_BONUS_CODE = 'bonus';

    const PROVIDER_HALYK = 10;
    const PROVIDER_KASPI = 11;
    const PROVIDER_EPS = 12;
    const PROVIDER_BONUS = 13;

    /** Statuses */
    const STATUS_NEW = 10;
    const STATUS_SUCCESS = 11;
    const STATUS_ERROR = 12;

    /** Types */
    const TYPE_INVOICE = 10;
    const TYPE_PAY = 11;
    const TYPE_RETURN = 12;

    /**
     * @return int[]
     */
    public static function getOnlineTypes(): array
    {
        return [
            self::PAYMENT_KASPI_ONLINE,
            self::PAYMENT_KASPI_INVOICE,
            self::PAYMENT_HALYK_ONLINE,
            self::PAYMENT_BCK_ONLINE,
            self::PAYMENT_AIRBA_PAY,
        ];
    }

    /**
     * @return string[]
     */
    public static function getMethods(): array
    {
        return [
            self::METHOD_MIXED => 'Смешанный',
            self::PAYMENT_CASH => 'Наличными',
            self::PAYMENT_KASPI_INVOICE => 'Kaspi удаленный счет',
            self::PAYMENT_HALYK_ONLINE => 'Онлайн Halyk Банк',
            self::PAYMENT_KASPI_ONLINE => 'Онлайн Kaspi Банк',
            self::PAYMENT_BCK_ONLINE => 'Онлайн БЦК Банк',
            self::PAYMENT_AIRBA_PAY => 'Онлайн Airba Pay',

            self::PAYMENT_KASPI_SHOP => 'Kaspi Shop',
            self::PAYMENT_JUSAN => 'Jusan Market',
            self::PAYMENT_HALYK_MARKET => 'Halyk Market',
            self::PAYMENT_WOLT => 'Wolt',
            self::PAYMENT_GLOVO => 'Glovo',
            self::PAYMENT_YANDEX_EDA => 'Yandex Eda',
            self::METHOD_BANK_INVOICE => 'Перечисление через банк (юр лица)',
            self::METHOD_EPS => 'Сертификат (ЭПС)',
            self::METHOD_OZON => 'Озон Market',
            self::METHOD_BONUS => 'Бонусами',
            self::METHOD_WB => 'Wildberries',
            self::METHOD_FORTE => 'Forte Market',
        ];
    }

    /**
     * @return string[]
     */
    public static function getCartMethods(): array
    {
        return [
            self::PAYMENT_CASH => 'Наличными',
            self::PAYMENT_KASPI_INVOICE => 'Kaspi удаленный счет',
        ];
    }

    /**
     * @param $method
     * @return string|null
     * @throws Exception
     */
    public static function getMethodName($method): ?string
    {
        return ArrayHelper::getValue(static::getMethods(), $method);
    }

    public static function maskTransactionId(string $transactionId): string
    {
        $len = strlen($transactionId);
        $maskCount = min(4, $len);
        $visiblePart = substr($transactionId, 0, $len - $maskCount);
        $maskedPart = str_repeat('*', $maskCount);

        return $visiblePart . $maskedPart;
    }
}