<?php

namespace app\modules\moderation\services;

use DomainException;
use RuntimeException;
use Throwable;
use Yii;
use app\core\helpers\PhoneHelper;
use app\modules\auth\helpers\UserHelper;
use app\modules\moderation\clients\OpenAiResponsesClient;
use app\modules\order\enums\OrderHistoryEvent;
use app\modules\order\enums\OrderStatus;
use app\modules\order\models\Order;
use app\modules\order\models\OrderEvent;
use app\modules\order\models\OrderHistory;
use app\services\order\OrderEventService;

final class OrderModerationService
{
    private const EVENT_SCHEMA_VERSION = 1;

    public function __construct(
        private OrderContextBuilder $contextBuilder,
        private OpenAiResponsesClient $client,
        private ModerationMessageBuilder $messageBuilder,
        private string $promptVersion,
    ) {
    }

    public function moderate(Order $order, bool $force = false): ?OrderEvent
    {
        $lockName = 'order-moderation:' . (int) $order->id;
        if (!Yii::$app->mutex->acquire($lockName, 1)) {
            return null;
        }

        $bot = null;
        $inputHash = null;

        try {
            $order->refresh();
            $this->assertPublished($order);

            $context = $this->contextBuilder->build($order);
            $inputHash = $this->inputHash($context);

            if (!$force && $this->hasResultForInput($order, $inputHash)) {
                return null;
            }

            $bot = UserHelper::getBot();
            $apiResult = $this->client->moderate(
                $context,
                $this->safetyIdentifier($order)
            );

            $result = $apiResult['result'];
            $meta = $apiResult['meta'];
            $eventData = $this->completedEventData($result, $meta, $context, $inputHash);

            $event = (new OrderEventService($order, $bot))->create(
                $this->messageBuilder->build($result),
                OrderHistoryEvent::MODERATION_COMPLETED->value,
                $eventData,
            );

            if ($event === null) {
                throw new RuntimeException('Published order has no history for moderation event.');
            }

            return $event;
        } catch (Throwable $e) {
            Yii::error([
                'message' => 'Order moderation failed.',
                'order_id' => (int) $order->id,
                'exception' => get_class($e),
                'error' => $e->getMessage(),
            ], __METHOD__);

            if ($bot === null) {
                throw $e;
            }

            $event = (new OrderEventService($order, $bot))->create(
                'AI-модерация не выполнена: требуется повторная проверка.',
                OrderHistoryEvent::MODERATION_FAILED->value,
                [
                    'schema_version' => self::EVENT_SCHEMA_VERSION,
                    'provider' => 'openai',
                    'prompt_version' => $this->promptVersion,
                    'input_hash' => $inputHash,
                    'error_code' => $this->errorCode($e),
                    'checked_at' => gmdate(DATE_ATOM),
                    'automated_action_applied' => false,
                ],
            );

            if ($event === null) {
                throw new RuntimeException(
                    'Unable to create failed moderation event.',
                    0,
                    $e
                );
            }

            return $event;
        } finally {
            Yii::$app->mutex->release($lockName);
        }
    }

    private function assertPublished(Order $order): void
    {
        $published = OrderHistory::find()
            ->andWhere([
                'order_id' => $order->id,
                'status_after' => OrderStatus::NEW->value,
            ])
            ->exists();

        if (!$published) {
            throw new DomainException('Order has not been published yet.');
        }
    }

    private function hasResultForInput(Order $order, string $inputHash): bool
    {
        $events = OrderEvent::find()
            ->andWhere([
                'order_id' => $order->id,
                'type' => [
                    OrderHistoryEvent::MODERATION_COMPLETED->value,
                    OrderHistoryEvent::MODERATION_FAILED->value,
                ],
            ])
            ->orderBy(['id' => SORT_DESC])
            ->limit(20)
            ->all();

        foreach ($events as $event) {
            $data = $this->eventData($event->data);
            if (($data['input_hash'] ?? null) === $inputHash) {
                return true;
            }
        }

        return false;
    }

    private function completedEventData(
        array $result,
        array $meta,
        array $context,
        string $inputHash
    ): array {
        return [
            'schema_version' => self::EVENT_SCHEMA_VERSION,
            'provider' => 'openai',
            'model' => $meta['model'] ?? null,
            'prompt_version' => $this->promptVersion,
            'verdict' => $result['verdict'],
            'scope' => $result['scope'],
            'recommended_action' => $result['recommended_action'],
            'reason_codes' => $result['reason_codes'],
            'checks' => $result['checks'],
            'evidence' => $result['evidence'],
            'summary' => $result['summary'],
            'signals' => $context['customer_behavior'],
            'related_order_ids' => array_values(array_unique(array_merge(
                $context['customer_behavior']['duplicate_order_ids'] ?? [],
                array_column($context['recent_orders'] ?? [], 'id'),
            ))),
            'input_hash' => $inputHash,
            'response_id' => $meta['response_id'] ?? null,
            'usage' => $meta['usage'] ?? [],
            'checked_at' => gmdate(DATE_ATOM),
            'automated_action_applied' => false,
        ];
    }

    private function inputHash(array $context): string
    {
        $stableContext = $context;
        foreach (($stableContext['recent_orders'] ?? []) as &$recentOrder) {
            unset($recentOrder['age_minutes']);
        }
        unset($recentOrder);

        $stableContext = $this->canonicalize($stableContext);
        $json = json_encode(
            [
                'prompt_version' => $this->promptVersion,
                'context' => $stableContext,
            ],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        if (!is_string($json)) {
            throw new RuntimeException('Failed to hash moderation input.');
        }

        return hash('sha256', $json);
    }

    private function safetyIdentifier(Order $order): string
    {
        $key = Yii::$app->params['config.encryption.key'] ?? null;
        if (!is_string($key) || $key === '') {
            throw new RuntimeException('config.encryption.key not set in params.');
        }

        $phone = (string) PhoneHelper::getCleanNumber($order->phone);

        return hash_hmac(
            'sha256',
            strtolower((string) $order->country_code) . ':' . $phone,
            $key
        );
    }

    private function eventData(mixed $data): array
    {
        if (is_array($data)) {
            return $data;
        }

        if (is_string($data)) {
            $decoded = json_decode($data, true);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
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

    private function errorCode(Throwable $e): string
    {
        $message = $e->getMessage();

        return match (true) {
            str_contains($message, 'status 401'),
            str_contains($message, 'status 403') => 'provider_auth_error',
            str_contains($message, 'status 429') => 'provider_rate_limit',
            preg_match('/status 5\\d\\d/', $message) === 1 => 'provider_unavailable',
            $e instanceof DomainException => 'configuration_or_state_error',
            default => 'processing_error',
        };
    }
}
