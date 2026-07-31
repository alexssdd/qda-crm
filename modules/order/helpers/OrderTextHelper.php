<?php

namespace app\modules\order\helpers;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\order\models\OrderEvent;
use app\modules\order\enums\ExecutorMatchSource;
use app\modules\order\enums\OrderHistoryEvent;

class OrderTextHelper
{
    public static function getMessage(OrderEvent $event): string
    {
        // Bid
        if ($event->type === OrderHistoryEvent::BID_CREATE->value) {
            $bidId = ArrayHelper::getValue($event->data, 'bid_id');
            $price = ArrayHelper::getValue($event->data, 'price');
            $executor = ArrayHelper::getValue($event->data, 'executor');
            $header = 'Новый отклик';

            $body = '';

            if ($executor) {
                $body .= "<span class='order-chat__detail'>Исполнитель: " . Html::encode((string) $executor) . "</span>";
            }

            if ($price) {
                $body .= "<span class='order-chat__detail'>Цена: " . Html::encode((string) $price) . "</span>";
            }

            return self::renderEvent($header, $body);
        }

        // Итоги матчинга исполнителей (отчёт pro-инстанса)
        if ($event->type === OrderHistoryEvent::EXECUTORS_NOTIFIED->value) {
            $matched = (int) ArrayHelper::getValue($event->data, 'matched', 0);
            $pushed = (int) ArrayHelper::getValue($event->data, 'pushed', 0);
            $night = (bool) ArrayHelper::getValue($event->data, 'night', false);
            $breakdown = (array) ArrayHelper::getValue($event->data, 'breakdown', []);

            $header = 'Подбор исполнителей';

            if ($matched === 0) {
                return self::renderEvent($header, "<span class='order-chat__detail'>Подходящих исполнителей не найдено</span>");
            }

            $sources = [];
            foreach (ExecutorMatchSource::cases() as $source) {
                $count = (int) ($breakdown[$source->value] ?? 0);
                if ($count > 0) {
                    $sources[$source->getLabel()] = $count;
                }
            }

            $body = "<span class='order-chat__detail'>Подобрано: {$matched}</span>";

            if ($sources) {
                $parts = [];
                foreach ($sources as $label => $count) {
                    $parts[] = "{$label} — {$count}";
                }
                $body .= "<span class='order-chat__detail'>" . Html::encode(implode(', ', $parts)) . "</span>";
            }

            $pushLine = "Отправлено пушей: {$pushed}";
            if ($night) {
                $pushLine .= ' (тихие часы — пуши не отправлялись)';
            }
            $body .= "<span class='order-chat__detail'>" . Html::encode($pushLine) . "</span>";

            return self::renderEvent($header, $body);
        }

        return Html::encode($event->message);
    }

    private static function renderEvent(string $header, string $body): string
    {
        return $header . "<div class='order-chat__details'>" . $body . '</div>';
    }
}
