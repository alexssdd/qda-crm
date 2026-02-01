<?php

namespace app\modules\order\helpers;

use Yii;
use Exception;
use yii\helpers\ArrayHelper;

/**
 * Order event helper
 */
class OrderEventHelper
{
    /**
     * @param $type
     * @return string|null
     * @throws Exception
     */
    public static function getIconClass($type): ?string
    {
        $data = [];

        return ArrayHelper::getValue($data, $type);
    }

    /**
     * @param $type
     * @return string|null
     * @throws Exception
     */
    public static function getImage($type): ?string
    {
        $data = [];

        return ArrayHelper::getValue($data, $type);
    }

    /**
     * @param $type
     * @return string
     */
    public static function getPriorityClass($type): string
    {
        $classArray = [];
        $importantTypes = [];
        $successfulTypes = [];
        $dangerTypes = [];
        $infoTypes = [];

        if (in_array($type, $importantTypes)){
            $classArray[] = 'order-chat__item--important';
        }
        if (in_array($type, $successfulTypes)){
            $classArray[] = 'order-chat__item--successful';
        }
        if (in_array($type, $dangerTypes)){
            $classArray[] = 'order-chat__item--danger';
        }
        if (in_array($type, $infoTypes)){
            $classArray[] = 'order-chat__item--info';
        }

        return implode(' ', $classArray);
    }
}