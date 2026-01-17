<?php

namespace app\core\rules;

use app\core\helpers\OrderHelper;

/**
 * Order rules
 */
class OrderRules
{
    /**
     * @param $status
     * @return bool
     */
    public static function canSave($status): bool
    {
        if (!in_array($status, [OrderHelper::STATUS_DELIVERED, OrderHelper::STATUS_ISSUED])){
            return true;
        }

        return false;
    }

    /**
     * @param $channel
     * @return bool
     */
    public static function canDeleteProduct($channel): bool
    {
        $forbiddenChannels = [

        ];

        if (in_array($channel, $forbiddenChannels)) {
            return false;
        }

        return true;
    }

    /**
     * @param $channel
     * @return bool
     */
    public static function canAddProduct($channel): bool
    {
        $forbiddenChannels = [

        ];
        $allowedChannels = [
            OrderHelper::CHANNEL_CRM,
        ];

        if (in_array($channel, $allowedChannels)) {
            return true;
        }

        return false;
    }

    /**
     * @param $channel
     * @return bool
     */
    public static function canUpdateProduct($channel): bool
    {
        $forbiddenChannels = [

        ];

        if (in_array($channel, $forbiddenChannels)) {
            return false;
        }

        return true;
    }
}