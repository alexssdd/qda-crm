<?php

namespace app\core\helpers;

use Yii;
use Exception;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * Advert helper
 */
class AdvertHelper
{
    /** Statuses */
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 11;

    /**
     * @return array status labels indexed by status values
     */
    public static function getStatusArray(): array
    {
        return [
            self::STATUS_ACTIVE => Yii::t('app', 'STATUS_ACTIVE'),
            self::STATUS_INACTIVE => Yii::t('app', 'STATUS_INACTIVE')
        ];
    }

    /**
     * @param $status
     * @return string
     * @throws Exception
     */
    public static function getStatusLabel($status): string
    {
        switch ($status) {
            case self::STATUS_ACTIVE:
                $class = 'label label-success';
                break;
            case self::STATUS_INACTIVE:
                $class = 'label label-danger';
                break;
            default:
                $class = 'label label-default';
        }

        return Html::tag('span', ArrayHelper::getValue(self::getStatusArray(), $status), [
            'class' => $class,
        ]);
    }
}