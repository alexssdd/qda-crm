<?php
namespace app\modules\order\helpers;

use app\modules\order\enums\OrderStatus;
use app\modules\order\enums\OrderChannel;
use yii\helpers\ArrayHelper;

class OrderHelper
{
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
}