<?php

namespace app\modules\moderation\commands;

use Throwable;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\ActiveQuery;
use app\modules\moderation\Module;
use app\modules\order\enums\OrderHistoryEvent;
use app\modules\order\enums\OrderStatus;
use app\modules\order\models\Order;
use app\modules\order\models\OrderEvent;
use app\modules\order\models\OrderHistory;

final class OrderController extends Controller
{
    public $defaultAction = 'run';

    public ?int $limit = null;
    public ?int $lookbackDays = null;
    public ?int $orderId = null;
    public bool $force = false;

    public function options($actionID): array
    {
        return array_merge(parent::options($actionID), [
            'limit',
            'lookbackDays',
            'orderId',
            'force',
        ]);
    }

    public function optionAliases(): array
    {
        return [
            'l' => 'limit',
            'd' => 'lookbackDays',
            'i' => 'orderId',
            'f' => 'force',
        ];
    }

    public function actionRun(): int
    {
        /** @var Module $module */
        $module = $this->module;

        if ($this->force && $this->orderId === null) {
            $this->stderr("--force разрешён только вместе с --order-id.\n");

            return ExitCode::USAGE;
        }

        $limit = $this->limit ?? $module->batchSize;
        $lookbackDays = $this->lookbackDays ?? $module->lookbackDays;
        if ($limit < 1 || $limit > 500 || $lookbackDays < 1) {
            $this->stderr("--limit должен быть от 1 до 500, --lookback-days — больше нуля.\n");

            return ExitCode::USAGE;
        }

        try {
            $service = $module->getOrderModerationService();
        } catch (Throwable $e) {
            $this->stderr("Не удалось инициализировать модерацию: {$e->getMessage()}\n");

            return ExitCode::CONFIG;
        }

        if ($this->orderId !== null) {
            $order = Order::findOne($this->orderId);
            if ($order === null) {
                $this->stderr("Заказ #{$this->orderId} не найден.\n");

                return ExitCode::NOINPUT;
            }

            try {
                $event = $service->moderate($order, $this->force);
            } catch (Throwable $e) {
                $this->stderr("Заказ #{$order->id}: {$e->getMessage()}\n");

                return ExitCode::UNSPECIFIED_ERROR;
            }

            if ($event === null) {
                $this->stdout("Заказ #{$order->id}: пропущен, результат уже существует или заказ занят.\n");

                return ExitCode::OK;
            }

            $this->stdout("Заказ #{$order->id}: создано событие {$event->type} (#{$event->id}).\n");

            return $event->type === OrderHistoryEvent::MODERATION_FAILED->value
                ? ExitCode::UNSPECIFIED_ERROR
                : ExitCode::OK;
        }

        $orders = $this->candidateQuery($lookbackDays)
            ->limit($limit)
            ->all();

        $completed = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($orders as $order) {
            try {
                $event = $service->moderate($order);
                if ($event === null) {
                    $skipped++;
                    continue;
                }

                if ($event->type === OrderHistoryEvent::MODERATION_COMPLETED->value) {
                    $completed++;
                } else {
                    $failed++;
                }
            } catch (Throwable $e) {
                $failed++;
                $this->stderr("Заказ #{$order->id}: {$e->getMessage()}\n");
            }
        }

        $this->stdout(sprintf(
            "AI-модерация: выбрано %d, завершено %d, ошибок %d, пропущено %d.\n",
            count($orders),
            $completed,
            $failed,
            $skipped
        ));

        return $failed > 0 ? ExitCode::UNSPECIFIED_ERROR : ExitCode::OK;
    }

    private function candidateQuery(int $lookbackDays): ActiveQuery
    {
        $publishedQuery = OrderHistory::find()
            ->alias('published_history')
            ->andWhere('published_history.order_id = moderation_order.id')
            ->andWhere(['published_history.status_after' => OrderStatus::NEW->value]);

        $terminalEventQuery = OrderEvent::find()
            ->alias('moderation_event')
            ->andWhere('moderation_event.order_id = moderation_order.id')
            ->andWhere([
                'moderation_event.type' => [
                    OrderHistoryEvent::MODERATION_COMPLETED->value,
                    OrderHistoryEvent::MODERATION_FAILED->value,
                ],
            ]);

        return Order::find()
            ->alias('moderation_order')
            ->andWhere(['>=', 'moderation_order.created_at', time() - $lookbackDays * 86400])
            ->andWhere(['exists', $publishedQuery])
            ->andWhere(['not exists', $terminalEventQuery])
            ->orderBy([
                'moderation_order.created_at' => SORT_ASC,
                'moderation_order.id' => SORT_ASC,
            ]);
    }
}
