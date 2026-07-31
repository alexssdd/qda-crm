<?php

namespace app\modules\order\helpers;

use app\modules\order\enums\OrderHistoryEvent;

/**
 * Order event helper
 */
class OrderEventHelper
{
    public const TYPE_TRANSFER = 2;
    public const TYPE_MESSAGE = 5;
    public const TYPE_CANCEL = 7;
    public const TYPE_PENDING = 8;
    public const TYPE_ASSEMBLY_CREATED = 10;

    /**
     * @param $type
     * @return string|null
     */
    public static function getIconClass($type): ?string
    {
        // прямой доступ вместо ArrayHelper::getValue — у ключей с точками (executors.notified)
        // на отсутствующем ключе сработал бы неявный dot-path fallback
        $data = [
            OrderHistoryEvent::EXECUTORS_NOTIFIED->value => 'icon-search',
        ];

        return $data[$type] ?? null;
    }

    /**
     * @param $type
     * @return string|null
     */
    public static function getImage($type): ?string
    {
        $data = [];

        return $data[$type] ?? null;
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
