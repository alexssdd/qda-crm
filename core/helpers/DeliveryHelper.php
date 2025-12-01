<?php

namespace app\core\helpers;

use Exception;
use yii\helpers\ArrayHelper;

/**
 * Delivery helper
 */
class DeliveryHelper
{
    /** Deliveries */
    const DELIVERY_PICKUP = 10;
    const DELIVERY_STANDARD = 11;
    const DELIVERY_EXPRESS = 12;
    const DELIVERY_KASPI = 13;
    const DELIVERY_KASPI_EXPRESS = 14;
    const DELIVERY_WOLT = 15;
    const DELIVERY_GLOVO = 16;
    const DELIVERY_YANDEX_EDA = 17;
    const DELIVERY_JUSAN = 18;
    const DELIVERY_HALYK = 19;
    const DELIVERY_OZON = 20;
    const DELIVERY_WB = 21;
    const DELIVERY_FORTE = 22;
    const DELIVERY_FORTE_EXPRESS = 23;
    const DELIVERY_EMEX = 24;
	const DELIVERY_WB_EXPRESS = 25;
    const DELIVERY_PICKUP_PARTNER = 26;
	const DELIVERY_WB_PICKUP = 27;

    /**
     * @return array[]
     */
    public static function getMethods(): array
    {
        return [
            self::DELIVERY_PICKUP => 'Самовывоз',
            self::DELIVERY_STANDARD => 'Стандартная доставка',
            self::DELIVERY_EXPRESS => 'Экспресс доставка',
            self::DELIVERY_KASPI => 'Доставка Kaspi',
            self::DELIVERY_KASPI_EXPRESS => 'Доставка Kaspi Express',
            self::DELIVERY_WOLT => 'Доставка Wolt',
            self::DELIVERY_GLOVO => 'Доставка Glovo',
            self::DELIVERY_YANDEX_EDA => 'Доставка Yandex Eda',
            self::DELIVERY_JUSAN => 'Доставка Jusan',
            self::DELIVERY_HALYK => 'Доставка Halyk',
            self::DELIVERY_OZON => 'Доставка Ozon',
            self::DELIVERY_WB => 'Доставка Wildberries',
            self::DELIVERY_FORTE => 'Доставка Forte',
            self::DELIVERY_FORTE_EXPRESS => 'Доставка Forte Express',
            self::DELIVERY_EMEX => 'Доставка Emex',
            self::DELIVERY_WB_EXPRESS => 'Доставка WB Express',
            self::DELIVERY_PICKUP_PARTNER => 'Доставка в пункт выдачи партнера',
            self::DELIVERY_WB_PICKUP => 'Самовывоз клиентом',
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

    /**
     * @return int[]
     */
    public static function getMethodsForAddress(): array
    {
        return [
            self::DELIVERY_STANDARD,
            self::DELIVERY_EXPRESS,
        ];
    }

    /**
     * @return int[]
     */
    public static function getMethodsForStore(): array
    {
        return [
            self::DELIVERY_PICKUP
        ];
    }
}