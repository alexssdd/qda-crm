<?php
namespace app\modules\order\helpers;

use app\helpers\PriceFormatter;
use app\modules\location\models\Country;
use app\modules\order\enums\OrderStatus;
use app\modules\order\enums\OrderChannel;
use app\modules\order\enums\OrderType;
use app\modules\order\enums\PriceType;
use app\modules\order\models\Order;
use Yii;
use yii\helpers\ArrayHelper;

class OrderHelper
{
    public static function getTypes(): array
    {
        return [
            OrderType::CARGO->value => Yii::t('app', 'order.type.cargo'),
            OrderType::TRUCK->value => Yii::t('app', 'order.type.truck'),
            OrderType::EVACUATOR->value => Yii::t('app', 'order.type.evacuator'),
            OrderType::MANIPULATOR->value => Yii::t('app', 'order.type.manipulator'),
            OrderType::EQUIPMENT->value => Yii::t('app', 'order.type.equipment'),
            OrderType::TRAIN->value => Yii::t('app', 'order.type.train')
        ];
    }

    public static function getTypeName($type)
    {
        return ArrayHelper::getValue(static::getTypes(), $type);
    }

    public static function getCountries(): array
    {
        $countries = Country::find()
            ->all();

        $result = [];

        /** @var Country $country */
        foreach ($countries as $country) {
            $result[$country->code] = $country->name;
        }

        return $result;
    }

    public static function getStatusName($status): ?string
    {
        return ArrayHelper::getValue(static::getStatuses(), $status);
    }

    public static function getChannel($channel): string
    {
        return ArrayHelper::getValue(static::getChannels(), $channel);
    }

    public static function getChannels(): array
    {
        return [
            OrderChannel::CRM->value => 'CRM',
            OrderChannel::BUSINESS->value => 'Business',
            OrderChannel::APP_IOS->value => 'App iOS',
            OrderChannel::APP_ANDROID->value => 'App Android',
        ];
    }

    public static function getStatuses(): array
    {
        return [
            OrderStatus::CREATED->value => 'Создан',
            OrderStatus::NEW->value => 'Новый',
            OrderStatus::PROGRESS->value => 'В обработке',
            OrderStatus::COMPLETED->value => 'Завершен',
            OrderStatus::CANCELLED->value => 'Отменен',
        ];
    }

    public static function getAvailableStatuses(Order $order): array
    {
        return [];
    }

    public static function isCompleted($status): bool
    {
        return in_array($status, [OrderStatus::CANCELLED->value, OrderStatus::COMPLETED->value]);
    }

    public static function getPriceLabel(Order $order): string
    {
        return match ($order->price_type) {
            PriceType::FIXED_SEMI_ID,
            PriceType::FIXED_ID => PriceFormatter::short($order->price, $order->country_code),
            PriceType::REQUEST_ID => Yii::t('app', 'order.price_type.request'),
            PriceType::CONTRACT_ID => Yii::t('app', 'order.price_type.contract'),
            default => Yii::t('app', 'order.price_type.unknown'),
        };
    }
}