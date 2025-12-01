<?php

namespace app\core\helpers;

use yii\helpers\ArrayHelper;

class OrderRegisterHelper
{
    /** Statuses */
    const STATUS_NEW = 10;
    const STATUS_ASSEMBLED = 11;
    const STATUS_FINISHED = 12;
    const STATUS_CANCELED = 13;

    /** Status codes */
    const STATUS_CODE_NEW = 'NEW';
    const STATUS_CODE_ASSEMBLED = 'ASSEMBLED';
    const STATUS_CODE_FINISHED = 'FINISHED';
    const STATUS_CODE_CANCELED = 'CANCELED';

    public static function getCodeByStatus($status): string
    {
        $data = [
            self::STATUS_NEW => self::STATUS_CODE_NEW,
            self::STATUS_ASSEMBLED => self::STATUS_CODE_ASSEMBLED,
            self::STATUS_FINISHED => self::STATUS_CODE_FINISHED,
            self::STATUS_CANCELED => self::STATUS_CODE_CANCELED,
        ];

        return ArrayHelper::getValue($data, $status);
    }

    public static function getStatusByCode($status): string
    {
        $data = [
            self::STATUS_CODE_NEW => self::STATUS_NEW,
            self::STATUS_CODE_ASSEMBLED => self::STATUS_ASSEMBLED,
            self::STATUS_CODE_FINISHED => self::STATUS_FINISHED,
            self::STATUS_CODE_CANCELED => self::STATUS_CANCELED,
        ];

        return ArrayHelper::getValue($data, $status);
    }
}