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
            OrderHelper::CHANNEL_MARKET,
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
            OrderHelper::CHANNEL_MARKET,
        ];
        $allowedChannels = [
            OrderHelper::CHANNEL_CRM,
            OrderHelper::CHANNEL_SITE_MARWIN,
            OrderHelper::CHANNEL_SITE_MELOMAN,
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
            OrderHelper::CHANNEL_MARKET,
        ];

        if (in_array($channel, $forbiddenChannels)) {
            return false;
        }

        return true;
    }
}