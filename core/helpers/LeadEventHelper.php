<?php

namespace app\core\helpers;

use Yii;
use Exception;
use yii\helpers\ArrayHelper;

/**
 * Lead event helper
 */
class LeadEventHelper
{
    // Common
    const TYPE_BOT = 1;
    const TYPE_TRANSFER = 2;
    const TYPE_TRANSFER_ERROR = 3;
    const TYPE_MESSAGE = 4;
    const TYPE_JIVOSITE_CREATED = 5;
    const TYPE_JIVOSITE_FINISHED = 6;
    const TYPE_JIVOSITE_CLIENT_UPDATED = 9;
    const TYPE_ORDER_CREATED = 7;
    const TYPE_CARE_CREATED = 8;

    /**
     * @return array
     */
    public static function getTypeArray(): array
    {
        return [
            self::TYPE_TRANSFER => Yii::t('lead', 'TYPE_TRANSFER'),
            self::TYPE_JIVOSITE_CREATED => Yii::t('lead', 'TYPE_JIVOSITE_CREATED'),
            self::TYPE_JIVOSITE_FINISHED => Yii::t('lead', 'TYPE_JIVOSITE_FINISHED'),
            self::TYPE_JIVOSITE_CLIENT_UPDATED => Yii::t('lead', 'TYPE_JIVOSITE_CLIENT_UPDATED'),
            self::TYPE_ORDER_CREATED => Yii::t('lead', 'TYPE_ORDER_CREATED'),
            self::TYPE_CARE_CREATED => Yii::t('lead', 'TYPE_CARE_CREATED'),
        ];
    }

    /**
     * @param $type
     * @return mixed
     * @throws Exception
     */
    public static function getTypeName($type)
    {
        return ArrayHelper::getValue(self::getTypeArray(), $type);
    }

    /**
     * @param $type
     * @return string|null
     * @throws Exception
     */
    public static function getIconClass($type): ?string
    {
        $data = [
            self::TYPE_TRANSFER => 'icon-arrow_forward',
            self::TYPE_TRANSFER_ERROR => 'icon-warning',
        ];

        return ArrayHelper::getValue($data, $type);
    }

    /**
     * @param $type
     * @return string
     */
    public static function getPriorityClass($type): string
    {
        $classArray = [];
        $importantTypes = [

        ];
        $successfulTypes = [];
        $dangerTypes = [];
        $infoTypes = [];

        if (in_array($type, $importantTypes)){
            $classArray[] = 'lead-chat__item--important';
        }
        if (in_array($type, $successfulTypes)){
            $classArray[] = 'lead-chat__item--successful';
        }
        if (in_array($type, $dangerTypes)){
            $classArray[] = 'lead-chat__item--danger';
        }
        if (in_array($type, $infoTypes)){
            $classArray[] = 'lead-chat__item--info';
        }

        return implode(' ', $classArray);
    }
}