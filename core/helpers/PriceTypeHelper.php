<?php

namespace app\core\helpers;

use Yii;
use Exception;
use yii\helpers\Html;
use yii\db\ActiveRecord;
use app\entities\Merchant;
use app\entities\PriceType;
use yii\helpers\ArrayHelper;

/**
 * Price type helper
 */
class PriceTypeHelper
{
    /** Codes */
    const CODE_COMMON = 'common';

    /** Statuses */
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 11;

    static $common;

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
        return PriceType::find()
            ->select(['name', 'id'])
            ->indexBy('id')
            ->column();
    }

    /**
     * @return array|ActiveRecord[]|Merchant[]
     */
    public static function getArrayByType(): array
    {
        return PriceType::find()
            ->indexBy('type')
            ->all();
    }

    /**
     * @return PriceType|ActiveRecord|null
     */
    public static function getPriceTypeCommon(): ?PriceType
    {
        if (self::$common === null){
            self::$common = PriceType::find()
                ->andWhere(['code' => self::CODE_COMMON])
                ->one();
        }

        return self::$common;
    }
}