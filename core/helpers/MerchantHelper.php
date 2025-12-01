<?php

namespace app\core\helpers;

use Yii;
use Exception;
use DomainException;
use yii\helpers\Html;
use yii\db\ActiveRecord;
use app\entities\Merchant;
use yii\helpers\ArrayHelper;

/**
 * Merchant helper
 */
class MerchantHelper
{
    /** Codes */
    const CODE_MARWIN = 'marwin';

    /** Kaspi */
    const KASPI_MHV = 'MHomeVideo';
    const KASPI_PTW = '30411672';

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
        return Merchant::find()
            ->select(['name', 'id'])
            ->indexBy('id')
            ->column();
    }

    /**
     * @return array|ActiveRecord[]|Merchant[]
     */
    public static function getArrayByCode(): array
    {
        return Merchant::find()
            ->indexBy('code')
            ->all();
    }

    /**
     * @param Merchant $merchant
     * @return mixed
     * @throws Exception
     */
    public static function getLegalName(Merchant $merchant)
    {
        return ArrayHelper::getValue($merchant->config, 'legal_name');
    }

    /**
     * @param $account
     * @return mixed
     * @throws Exception
     */
    public static function getKaspiMerchantByAccount($account): mixed
    {
        $data = [
            OrderHelper::ACCOUNT_MHV => self::KASPI_MHV,
            OrderHelper::ACCOUNT_PTW => self::KASPI_PTW
        ];

        return ArrayHelper::getValue($data, $account);
    }
}