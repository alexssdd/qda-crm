<?php

namespace app\core\helpers;

use Yii;
use Exception;
use app\entities\Order;
use yii\helpers\ArrayHelper;
use app\entities\OrderProduct;

/**
 * Order helper
 */
class OrderHelper
{
    // Processed
    const STATUS_NEW = 10;
    const STATUS_ACCEPTED = 11;

    // Delivery
    const STATUS_SHIPPED = 12;
    const STATUS_COURIER = 13;
    const STATUS_DELIVERED = 14;

    // Pickup
    const STATUS_PICKUP = 15;
    const STATUS_ISSUED = 16;

    // Canceled
    const STATUS_CANCELLED = 17;

    /** Channels */
    const CHANNEL_CRM = 10;
    const CHANNEL_APP_IOS = 24;
    const CHANNEL_APP_ANDROID = 25;

    /**
     * @return string[]
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW => 'Новый',
            self::STATUS_ACCEPTED => 'Принят',
            self::STATUS_SHIPPED => 'На доставку',
            self::STATUS_COURIER => 'Курьер принял',
            self::STATUS_PICKUP => 'Самовывоз',
            self::STATUS_DELIVERED => 'Доставлен',
            self::STATUS_ISSUED => 'Выдан клиенту',
            self::STATUS_CANCELLED => 'Отменен',
        ];
    }

    /**
     * @param $status
     * @return string|null
     * @throws Exception
     */
    public static function getStatusName($status): ?string
    {
        return ArrayHelper::getValue(self::getStatuses(), $status);
    }

    /**
     * @return string[]
     */
    public static function getChannels(): array
    {
        return [
            self::CHANNEL_CRM => 'CRM',
            self::CHANNEL_APP_IOS => 'App iOS',
            self::CHANNEL_APP_ANDROID => 'App Android',
        ];
    }

    /**
     * @param $channel
     * @return string
     * @throws Exception
     */
    public static function getChannel($channel): string
    {
        return ArrayHelper::getValue(static::getChannels(), $channel);
    }

    /**
     * @param Order $order
     * @return string|null
     */
    public static function getHandlerName(Order $order): ?string
    {
        return $order->handler ? UserHelper::getShortName($order->handler) : null;
    }

    /**
     * @param Order $order
     * @return string|null
     * @throws Exception
     */
    public static function getCreated(Order $order): ?string
    {
        return Yii::$app->formatter->asDatetime($order->created_at);
    }

    /**
     * @param Order $order
     * @return float
     */
    public static function getAmount(Order $order): float
    {
        return floor($order->amount);
    }

    /**
     * @param Order $order
     * @return float
     */
    public static function getAmountTotal(Order $order): float
    {
        return floor($order->amount + $order->delivery_cost);
    }

    /**
     * @param Order $order
     * @return string
     * @throws Exception
     */
    public static function getCostTotalLabel(Order $order): string
    {
        return Yii::$app->formatter->asDecimal(static::getAmountTotal($order)) . ' ₸';
    }

    /**
     * @param Order $order
     * @return string|null
     * @throws Exception
     */
    public static function getDeliveryLabel(Order $order): ?string
    {
        if (!$order->delivery_method) {
            return null;
        }

        $name = DeliveryHelper::getMethodName($order->delivery_method);

        if ($order->store) {
            return Yii::t('app', '{name} from store #{store}', ['name' => $name, 'store' => StoreHelper::getNameShort($order->store)]);
        }

        return $name;
    }

    /**
     * @param Order $order
     * @return string|null
     * @throws Exception
     */
    public static function getPaymentLabel(Order $order): ?string
    {
        if (!$order->payment_method) {
            return null;
        }

        if ($order->payment_method == PaymentHelper::METHOD_MIXED) {
            $result = [];
            $methods = self::getPaymentMethods($order);

            foreach ($methods as $method) {
                $result[] = PaymentHelper::getMethodName($method['method']);
            }

            return implode(' + ', $result);
        }

        return PaymentHelper::getMethodName($order->payment_method);
    }

    /**
     * @param Order $order
     * @return mixed
     * @throws Exception
     */
    public static function getPaymentMethods(Order $order): mixed
    {
        return ArrayHelper::getValue($order->extra_fields, 'payment_methods');
    }

    /**
     * @param Order $order
     * @return array|null[]|string[]
     * @throws Exception
     */
    public static function getAvailableStatuses(Order $order): array
    {
        $isAdmin = UserHelper::isAdmin();

        switch ($order->status) {
            case self::STATUS_NEW:
                return [
                    self::STATUS_NEW => self::getStatusName(self::STATUS_NEW),
                    self::STATUS_ACCEPTED => self::getStatusName(self::STATUS_ACCEPTED),
                ];
            case self::STATUS_ACCEPTED:
                return [
                    self::STATUS_ACCEPTED => self::getStatusName(self::STATUS_ACCEPTED),
                    self::STATUS_SHIPPED => self::getStatusName(self::STATUS_SHIPPED),
                    self::STATUS_PICKUP => self::getStatusName(self::STATUS_PICKUP)
                ];
            case self::STATUS_SHIPPED:
                if ($isAdmin){
                    return [
                        self::STATUS_SHIPPED => self::getStatusName(self::STATUS_SHIPPED),
                        self::STATUS_COURIER => self::getStatusName(self::STATUS_COURIER),
                        self::STATUS_DELIVERED => self::getStatusName(self::STATUS_DELIVERED),
                    ];
                }

                return [
                    self::STATUS_SHIPPED => self::getStatusName(self::STATUS_SHIPPED),
                    self::STATUS_COURIER => self::getStatusName(self::STATUS_COURIER),
                ];
            case self::STATUS_COURIER:
                return [
                    self::STATUS_COURIER => self::getStatusName(self::STATUS_COURIER),
                    self::STATUS_DELIVERED => self::getStatusName(self::STATUS_DELIVERED),
                ];
            case self::STATUS_DELIVERED:
                return [
                    self::STATUS_DELIVERED => self::getStatusName(self::STATUS_DELIVERED),
                ];
            case self::STATUS_PICKUP:
                return [
                    self::STATUS_PICKUP => self::getStatusName(self::STATUS_PICKUP),
                    self::STATUS_ISSUED => self::getStatusName(self::STATUS_ISSUED),
                ];
            case self::STATUS_ISSUED:
                return [
                    self::STATUS_ISSUED => self::getStatusName(self::STATUS_ISSUED),
                ];
            case self::STATUS_CANCELLED:
                return [
                    self::STATUS_CANCELLED => self::getStatusName(self::STATUS_CANCELLED),
                ];
        }
        return [];
    }

    /**
     * @param $status
     * @return bool
     */
    public static function isCompleted($status): bool
    {
        return in_array($status, [self::STATUS_CANCELLED, self::STATUS_DELIVERED, self::STATUS_ISSUED]);
    }

    /**
     * @param OrderProduct $product
     * @return mixed
     * @throws Exception
     */
    public static function getType(OrderProduct $product): mixed
    {
        return ArrayHelper::getValue($product->extra_fields, 'type');
    }
}