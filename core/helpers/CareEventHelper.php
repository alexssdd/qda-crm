<?php

namespace app\core\helpers;

use Yii;
use Exception;
use app\entities\CareEvent;
use yii\helpers\ArrayHelper;

/**
 * Care event helper
 */
class CareEventHelper
{
    // Common
    const TYPE_BOT = 1;
    const TYPE_TRANSFER = 2;
    const TYPE_TRANSFER_ERROR = 3;
    const TYPE_MESSAGE = 4;
    const TYPE_CARE_TEXT = 5;
    const TYPE_CARE_SOLUTION_TEXT = 6;

    /**
     * @return array
     */
    public static function getTypeArray(): array
    {
        return [
            self::TYPE_TRANSFER => Yii::t('care', 'TYPE_TRANSFER')
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
            $classArray[] = 'care-chat__item--important';
        }
        if (in_array($type, $successfulTypes)){
            $classArray[] = 'care-chat__item--successful';
        }
        if (in_array($type, $dangerTypes)){
            $classArray[] = 'care-chat__item--danger';
        }
        if (in_array($type, $infoTypes)){
            $classArray[] = 'care-chat__item--info';
        }

        return implode(' ', $classArray);
    }

    /**
     * @param CareEvent $event
     * @return mixed|string|null
     * @throws Exception
     */
    public static function getMessage(CareEvent $event)
    {
        if ($event->message){
            return $event->message;
        }

        return self::getTypeName($event->type);
    }

    /**
     * @param $text
     * @return string
     */
    public static function format($text): string
    {
        $text = str_replace("\r", '<br />', $text);
        $text = str_replace("\n", '<br />', $text);

        return trim($text);
    }
}