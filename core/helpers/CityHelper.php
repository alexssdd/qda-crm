<?php

namespace app\core\helpers;

use Yii;
use Exception;
use yii\helpers\Html;
use app\entities\City;
use yii\helpers\ArrayHelper;

/**
 * City helper
 */
class CityHelper
{
    /** Ids */
    const ID_ALL              = 1;
    const ID_ALMATY           = 2;
    const ID_ASTANA           = 3;
    const ID_SHYMKENT         = 4;
    const ID_AKTOBE           = 5;
    const ID_AKTAU            = 6;
    const ID_ATYRAU           = 7;
    const ID_KARAGANDA        = 8;
    const ID_KOSTANAY         = 9;
    const ID_PAVLODAR         = 10;
    const ID_PETROPAVLOVSK    = 11;
    const ID_SEMEY            = 12;
    const ID_TARAZ            = 13;
    const ID_UST_KAMENOGORSK  = 14;
    const ID_URALSK           = 15;

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

    /**
     * @return array
     */
    public static function getSelectArray(): array
    {
        return City::find()
            ->select(['name', 'id'])
            ->andWhere(['not', ['id' => self::ID_ALL]])
            ->indexBy('id')
            ->column();
    }

    /**
     * @param City $city
     * @return int|null
     * @throws Exception
     */
    public static function getVendorId(City $city): ?int
    {
        return ArrayHelper::getValue($city->config, 'market_id');
    }
}