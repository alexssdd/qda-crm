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
            $header = 'Новый отклик';

            $body = '';

            if ($price) {
                $body .= "<span class='order-chat__detail'>Цена: " . $price . "</span>";
            }

            return $header . "<div class='order-chat__details'>" . $body . '</div>';
        }

        return Html::encode($event->message);
    }
}