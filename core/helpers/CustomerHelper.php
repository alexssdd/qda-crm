<?php

namespace app\core\helpers;

use Yii;
use Exception;
use yii\helpers\Html;
use app\entities\Order;
use app\entities\Customer;
use yii\helpers\ArrayHelper;

/**
 * Customer helper
 */
class CustomerHelper
{
    /** Types */
    const TYPE_INDIVIDUAL = 10;
    const TYPE_LEGAL = 11;

    /** Statuses */
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 11;

    const PAVEL_ID = 2;
    const ANEL_ID = 17;
    const ROZA_ID = 21;

    /**
     * @return array
     */
    public static function getTypeArray(): array
    {
        return [
            self::TYPE_INDIVIDUAL => Yii::t('app', 'TYPE_INDIVIDUAL'),
            self::TYPE_LEGAL => Yii::t('app', 'TYPE_LEGAL')
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
     * @param Customer $customer
     * @return mixed
     * @throws Exception
     */
    public static function getTelegramId(Customer $customer)
    {
        return ArrayHelper::getValue($customer->config, 'telegram_id');
    }

    /**
     * @param Customer $customer
     * @return array
     * @throws Exception
     */
    public static function getDetail(Customer $customer): array
    {
        /** @var Order[] $orders */
        $orders = Order::find()
            ->andWhere(['customer_id' => $customer->id])
            ->with(['city', 'products'])
            ->limit(15)
            ->all();

        $products = [];
        $cities = [];
        foreach ($orders as $order){
            // Products
            foreach ($order->products as $orderProduct){
                if (!array_key_exists($orderProduct->product_id, $products)){
                    $products[$orderProduct->sku] = [
                        'sku' => $orderProduct->sku,
                        'name' => $orderProduct->name,
                        'count' => 0
                    ];
                }

                $products[$orderProduct->sku]['count']++;
            }

            // Cities
            if (!array_key_exists($order->city_id, $cities)){
                $cities[$order->city_id] = [
                    'name' => $order->city->name,
                    'count' => 0
                ];
            }

            $cities[$order->city_id]['count']++;
        }

        // Sort
        ArrayHelper::multisort($products, 'count', SORT_DESC);
        ArrayHelper::multisort($cities, 'count', SORT_DESC);

        return [
            'orders' => $orders,
            'products' => array_values($products),
            'city' => ArrayHelper::getValue(array_values($cities), '0.name', '')
        ];
    }

    /**
     * @param Customer $customer
     * @return mixed
     * @throws Exception
     */
    public static function getVendorId(Customer $customer): mixed
    {
        return ArrayHelper::getValue($customer->config, 'vendor_id');
    }
}