<?php

namespace app\modules\moderation\services;

use app\core\helpers\PhoneHelper;
use app\entities\Customer;
use app\modules\order\enums\OrderStatus;
use app\modules\order\models\Order;
use app\modules\order\models\OrderHistory;

final class OrderContextBuilder
{
    private const RECENT_WINDOW_DAYS = 30;
    private const DUPLICATE_WINDOW_SECONDS = 86400;

    public function __construct(private int $recentOrdersLimit = 5)
    {
    }

    public function build(Order $order): array
    {
        $now = time();
        $phones = $this->phoneVariants((string) $order->phone);
        $baseQuery = Order::find()
            ->andWhere(['country_code' => $order->country_code])
            ->andWhere(['phone' => $phones]);

        $orders1h = (int) (clone $baseQuery)
            ->andWhere(['>=', 'created_at', $now - 3600])
            ->count();
        $orders24h = (int) (clone $baseQuery)
            ->andWhere(['>=', 'created_at', $now - 86400])
            ->count();
        $orders7d = (int) (clone $baseQuery)
            ->andWhere(['>=', 'created_at', $now - 7 * 86400])
            ->count();
        $orders30d = (int) (clone $baseQuery)
            ->andWhere(['>=', 'created_at', $now - self::RECENT_WINDOW_DAYS * 86400])
            ->count();
        $cancelled30d = (int) (clone $baseQuery)
            ->andWhere(['>=', 'created_at', $now - self::RECENT_WINDOW_DAYS * 86400])
            ->andWhere(['status' => OrderStatus::CANCELLED->value])
            ->count();
        $activeOrders = (int) (clone $baseQuery)
            ->andWhere([
                'status' => [
                    OrderStatus::CREATED->value,
                    OrderStatus::NEW->value,
                    OrderStatus::PROGRESS->value,
                ],
            ])
            ->count();

        $duplicateCandidates = (clone $baseQuery)
            ->andWhere(['<>', 'id', $order->id])
            ->andWhere(['>=', 'created_at', $now - self::DUPLICATE_WINDOW_SECONDS])
            ->orderBy(['id' => SORT_DESC])
            ->limit(100)
            ->all();

        $fingerprint = $this->fingerprint($order);
        $duplicateOrderIds = [];
        foreach ($duplicateCandidates as $candidate) {
            if ($this->fingerprint($candidate) === $fingerprint) {
                $duplicateOrderIds[] = (int) $candidate->id;
            }
        }

        $recentOrders = (clone $baseQuery)
            ->andWhere(['<>', 'id', $order->id])
            ->andWhere(['>=', 'created_at', $now - self::RECENT_WINDOW_DAYS * 86400])
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->limit(max(1, $this->recentOrdersLimit))
            ->all();

        $customer = Customer::find()
            ->andWhere(['country_code' => $order->country_code])
            ->andWhere(['phone' => $phones])
            ->orderBy(['last_order_at' => SORT_DESC, 'id' => SORT_DESC])
            ->one();

        return [
            'current_order' => $this->currentOrder($order),
            'customer_behavior' => [
                'orders_1h' => $orders1h,
                'orders_24h' => $orders24h,
                'orders_7d' => $orders7d,
                'orders_30d' => $orders30d,
                'active_orders' => $activeOrders,
                'cancelled_30d' => $cancelled30d,
                'cancellation_rate_30d' => $orders30d > 0
                    ? round($cancelled30d / $orders30d, 3)
                    : 0.0,
                'exact_duplicates_24h' => count($duplicateOrderIds),
                'duplicate_order_ids' => array_slice($duplicateOrderIds, 0, 20),
                'account_age_days' => $this->accountAgeDays($customer, $now),
                'lifetime_orders_created' => $customer ? (int) $customer->orders_created : null,
                'lifetime_orders_completed' => $customer ? (int) $customer->orders_completed : null,
                'lifetime_orders_cancelled' => $customer ? (int) $customer->orders_canceled : null,
            ],
            'recent_orders' => array_map(
                fn (Order $recentOrder): array => $this->recentOrder(
                    $recentOrder,
                    $fingerprint,
                    $now
                ),
                $recentOrders
            ),
            'privacy' => [
                'customer_name_sent' => false,
                'customer_phone_sent' => false,
                'coordinates_sent' => false,
            ],
        ];
    }

    private function currentOrder(Order $order): array
    {
        $publishedAt = OrderHistory::find()
            ->select('created_at')
            ->andWhere([
                'order_id' => $order->id,
                'status_after' => OrderStatus::NEW->value,
            ])
            ->orderBy(['id' => SORT_ASC])
            ->scalar();

        return [
            'id' => (int) $order->id,
            'number' => $this->text($order->number, 100),
            'country_code' => (string) $order->country_code,
            'channel' => (int) $order->channel,
            'type' => (int) $order->type,
            'category' => (int) $order->category,
            'status' => (int) $order->status,
            'customer_rating' => $order->rating !== null ? (float) $order->rating : null,
            'route' => [
                'from_name' => $this->text($order->from_name, 255),
                'from_address' => $this->text($order->from_address, 500),
                'to_name' => $this->text($order->to_name, 255),
                'to_address' => $this->text($order->to_address, 500),
            ],
            'price_type' => (int) $order->price_type,
            'price' => $order->price !== null ? (float) $order->price : null,
            'payment_method' => (int) $order->payment_method,
            'comment' => $this->text($order->comment, 1000),
            'extra_fields' => $this->safeExtraFields($order->extra_fields),
            'created_at' => $this->dateTime((int) $order->created_at),
            'published_at' => $publishedAt ? $this->dateTime((int) $publishedAt) : null,
        ];
    }

    private function recentOrder(Order $order, string $currentFingerprint, int $now): array
    {
        return [
            'id' => (int) $order->id,
            'age_minutes' => max(0, (int) floor(($now - (int) $order->created_at) / 60)),
            'status' => (int) $order->status,
            'type' => (int) $order->type,
            'category' => (int) $order->category,
            'route' => [
                'from_name' => $this->text($order->from_name, 255),
                'to_name' => $this->text($order->to_name, 255),
            ],
            'price' => $order->price !== null ? (float) $order->price : null,
            'comment' => $this->text($order->comment, 300),
            'same_content_as_current' => $this->fingerprint($order) === $currentFingerprint,
        ];
    }

    private function fingerprint(Order $order): string
    {
        $data = [
            'type' => (int) $order->type,
            'category' => (int) $order->category,
            'from_name' => $this->normalizeText($order->from_name),
            'from_address' => $this->normalizeText($order->from_address),
            'to_name' => $this->normalizeText($order->to_name),
            'to_address' => $this->normalizeText($order->to_address),
            'comment' => $this->normalizeText($order->comment),
            'extra_fields' => $this->canonicalize($this->safeExtraFields($order->extra_fields)),
        ];

        return hash(
            'sha256',
            (string) json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    private function phoneVariants(string $phone): array
    {
        $clean = (string) PhoneHelper::getCleanNumber($phone);
        $variants = array_filter([
            $phone,
            $clean,
            $clean !== '' ? '+' . $clean : null,
        ], static fn ($value): bool => is_string($value) && $value !== '');

        return array_values(array_unique($variants));
    }

    private function accountAgeDays(?Customer $customer, int $now): ?int
    {
        if ($customer === null) {
            return null;
        }

        $createdAt = $customer->registered_at ?: $customer->created_at;
        if (!$createdAt) {
            return null;
        }

        return max(0, (int) floor(($now - (int) $createdAt) / 86400));
    }

    private function safeExtraFields(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $result = [];
        if (isset($value['loaders_count']) && is_numeric($value['loaders_count'])) {
            $result['loaders_count'] = (int) $value['loaders_count'];
        }

        if (isset($value['scheduled_at']) && is_string($value['scheduled_at'])) {
            $result['scheduled_at'] = $this->text($value['scheduled_at'], 100);
        }

        return $result;
    }

    private function canonicalize(mixed $value): mixed
    {
        if (!is_array($value)) {
            return $value;
        }

        if (!array_is_list($value)) {
            ksort($value);
        }

        foreach ($value as $key => $item) {
            $value[$key] = $this->canonicalize($item);
        }

        return $value;
    }

    private function normalizeText(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }

        $value = mb_strtolower(trim($value));
        $value = preg_replace('/\s+/u', ' ', $value);

        return is_string($value) ? $value : '';
    }

    private function text(mixed $value, int $maxLength): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $value = $this->redactContactData($value);

        return mb_substr($value, 0, $maxLength);
    }

    private function redactContactData(string $value): string
    {
        $redacted = preg_replace(
            '/\b[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}\b/iu',
            '[email]',
            $value
        );
        if (is_string($redacted)) {
            $value = $redacted;
        }

        $redacted = preg_replace(
            '~(?<![\p{L}\p{N}])(?:https?://|www\.|t\.me/|telegram\.me/|wa\.me/|api\.whatsapp\.com/)\S+~iu',
            '[url]',
            $value
        );
        if (is_string($redacted)) {
            $value = $redacted;
        }

        $redacted = preg_replace(
            '~(?<![@\p{L}\p{N}])(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+(?:com|net|org|io|me|app|ru|kz|uz)(?:/\S*)?~iu',
            '[url]',
            $value
        );
        if (is_string($redacted)) {
            $value = $redacted;
        }

        $redacted = preg_replace(
            '/(?<![\d.])[+-]?\d{1,2}\.\d{3,}\s*[,;]\s*[+-]?\d{1,3}\.\d{3,}(?![\d.])/u',
            '[coordinates]',
            $value
        );
        if (is_string($redacted)) {
            $value = $redacted;
        }

        $redacted = preg_replace_callback(
            '/(?<![\p{L}\p{N}])\+?\d(?:[\s().\-]*\d){6,}(?![\p{L}\p{N}])/u',
            static function (array $matches): string {
                $digits = preg_replace('/\D+/u', '', $matches[0]);

                return is_string($digits) && strlen($digits) >= 7
                    ? '[phone]'
                    : $matches[0];
            },
            $value
        );
        if (is_string($redacted)) {
            $value = $redacted;
        }

        $redacted = preg_replace(
            '/(?<![\p{L}\p{N}])@[A-Z0-9_]{3,32}\b/iu',
            '[handle]',
            $value
        );

        return is_string($redacted) ? $redacted : $value;
    }

    private function dateTime(int $timestamp): ?string
    {
        return $timestamp > 0 ? gmdate(DATE_ATOM, $timestamp) : null;
    }
}
