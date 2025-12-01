<?php

namespace app\core\rules;

use app\core\helpers\LeadHelper;

/**
 * Lead rules
 */
class LeadRules
{
    /**
     * @param $status
     * @return bool
     */
    public static function canSave($status): bool
    {
        $statuses = [
            LeadHelper::STATUS_SUCCESS_B2B,
            LeadHelper::STATUS_SUCCESS_B2C,
            LeadHelper::STATUS_CLOSED,
            LeadHelper::STATUS_CANCELLED
        ];
        if (!in_array($status, $statuses)){
            return true;
        }

        return false;
    }
}