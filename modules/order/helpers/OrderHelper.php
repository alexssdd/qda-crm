<?php
namespace app\modules\order\helpers;

use app\helpers\PriceFormatter;
use app\modules\location\models\Country;
use app\modules\location\helpers\LocationHelper;
use app\modules\order\enums\OrderStatus;
use app\modules\order\enums\OrderChannel;
use app\modules\order\enums\OrderType;
use app\modules\order\enums\PaymentMethod;
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
            ->orderBy(['sort' => SORT_ASC, 'name' => SORT_ASC])
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
        return ArrayHelper::getValue(static::getChannels(), $channel, '—');
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

    public static function canTransfer($status): bool
    {
        return in_array($status, [
            OrderStatus::CREATED->value,
            OrderStatus::NEW->value,
            OrderStatus::PROGRESS->value,
        ], true);
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

    public static function getCreated(Order $order): ?string
    {
        if (!$order->created_at) {
            return null;
        }
        return Yii::$app->formatter->asDatetime($order->created_at);
    }

    public static function getShareUrl(Order $order): ?string
    {
        return $order->public_id ? 'https://goqda.com/order/' . $order->public_id : null;
    }

    /**
     * Готовый текст для отправки исполнителю. Формат и форматирование
     * зеркалят OG-карточку ссылки (site OrderPreviewService) — оператор видит
     * оба варианта в одном сообщении WhatsApp, расходиться им нельзя.
     */
    public static function getShareMessage(Order $order, bool $withUrl = true): ?string
    {
        $url = static::getShareUrl($order);
        if (!$url) {
            return null;
        }

        $head = implode(' · ', array_filter([
            static::getTypeName($order->type),
            static::getSharePriceLabel($order),
            $order->payment_method ? PaymentMethod::getLabelById((int) $order->payment_method) : null,
        ]));

        $route = implode(' → ', array_unique(array_filter([
            $order->locationFrom ? LocationHelper::getName($order->locationFrom->extra_fields, $order->locationFrom->name) : null,
            $order->locationTo ? LocationHelper::getName($order->locationTo->extra_fields, $order->locationTo->name) : null,
        ])));

        $lines = ['🔥 Заказ: ' . $head];
        if ($route !== '') {
            $lines[] = 'Направление: ' . $route;
        }
        if ($withUrl) {
            $lines[] = $url;
        }

        return implode("\n", $lines);
    }

    /**
     * Цена как в OG-карточке: полное число + «тг»/«сум», без «млн»-сокращений.
     */
    private static function getSharePriceLabel(Order $order): ?string
    {
        // Строки — как в site-словаре OG-карточки, не как CRM-шные
        // («По запросу»): в WhatsApp текст и карточка стоят рядом.
        $label = match ($order->price_type) {
            PriceType::REQUEST_ID => 'Цена по запросу',
            PriceType::CONTRACT_ID => 'Договорная',
            default => null,
        };

        if ($label !== null) {
            return $label;
        }

        if ($order->price === null) {
            return null;
        }

        $currency = match ($order->country_code) {
            'uz' => 'сум',
            default => 'тг',
        };

        return number_format((float) $order->price, 0, '.', ' ') . ' ' . $currency;
    }

    public static function getFromCountry(Order $order): ?string
    {
        if (!$order->locationFrom) {
            return null;
        }
        return $order->locationFrom->country->name;
    }

    public static function getToCountry(Order $order): ?string
    {
        if (!$order->locationTo) {
            return null;
        }
        return $order->locationTo->country->name;
    }

    public static function getFromLocation(Order $order): ?string
    {
        if (!$order->locationFrom) {
            return null;
        }
        return $order->locationFrom->name;
    }

    public static function getToLocation(Order $order): ?string
    {
        if (!$order->locationTo) {
            return null;
        }
        return $order->locationTo->name;
    }

    public static function getFromCoordinates(Order $order): ?string
    {
        if (!$order->from_lat || !$order->from_lng) {
            return null;
        }
        return "{$order->from_lat}, {$order->from_lng}";
    }

    public static function getToCoordinates(Order $order): ?string
    {
        if (!$order->to_lat || !$order->to_lng) {
            return null;
        }
        return "{$order->to_lat}, {$order->to_lng}";
    }

    public static function getAdditionalFields(Order $order): array
    {
        $result = [];

        foreach (($order->extra_fields ?? []) as $name => $value) {
            $result[] = [
                'name' => static::getAdditionalFieldName((string) $name),
                'value' => static::getAdditionalFieldValue($value),
            ];
        }

        return $result;
    }

    private static function getAdditionalFieldName(string $name): string
    {
        return match ($name) {
            'loaders_count' => 'Количество грузчиков',
            'scheduled_at' => 'Запланировано на',
            default => $name,
        };
    }

    private static function getAdditionalFieldValue($value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if (is_bool($value)) {
            return $value ? 'Да' : 'Нет';
        }

        if (is_array($value)) {
            return (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return (string) $value;
    }
}
