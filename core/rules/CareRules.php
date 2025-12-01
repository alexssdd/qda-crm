<?php

namespace app\core\rules;

use app\core\helpers\CareHelper;

/**
 * Care rules
 */
class CareRules
{
    /**
     * @param $status
     * @return bool
     */
    public static function canSave($status): bool
    {
        if (!in_array($status, [CareHelper::STATUS_FINISHED_GOOD, CareHelper::STATUS_FINISHED_BAD, CareHelper::STATUS_COULD_NOT_CALL])){
            return true;
        }

        return false;
    }
}