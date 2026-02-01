<?php

namespace app\modules\order\helpers;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\order\models\OrderEvent;
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
                $body .= "<span class='order-chat__detail'>Исполнитель: " . $executor . "</span>";
            }

            if ($price) {
                $body .= "<span class='order-chat__detail'>Цена: " . $price . "</span>";
            }

            return $header . "<div class='order-chat__details'>" . $body . '</div>';
        }

        return Html::encode($event->message);
    }
}