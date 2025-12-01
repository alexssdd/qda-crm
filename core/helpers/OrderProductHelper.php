<?php

namespace app\core\helpers;

use Exception;
use yii\helpers\ArrayHelper;
use app\entities\OrderProduct;

/**
 * Order product helper
 */
class OrderProductHelper
{
    /**
     * @param OrderProduct $orderProduct
     * @return float
     * @throws Exception
     */
    public static function getBonus(OrderProduct $orderProduct): float
    {
        return ArrayHelper::getValue($orderProduct->extra_fields, 'bonus', 0);
    }
}