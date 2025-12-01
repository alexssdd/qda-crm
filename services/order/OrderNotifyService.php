<?php

namespace app\services\order;

use Yii;
use app\entities\Order;
use app\modules\sms\jobs\SmsJob;
use app\modules\sms\SmsTemplate;
use app\core\helpers\OrderHelper;
use app\core\helpers\PaymentHelper;

/**
 * Order notify service
 */
class OrderNotifyService
{
    private $_order;

    /**
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->_order = $order;
    }

    /**
     * @return void
     */
    public function newOrder()
    {
        $order = $this->_order;

        // Check payment method
        if (!in_array($order->payment_method, [PaymentHelper::PAYMENT_HALYK_ONLINE, PaymentHelper::PAYMENT_KASPI_ONLINE, PaymentHelper::PAYMENT_BCK_ONLINE])) {
            return;
        }

        $link = OrderHelper::getPayLink($order->number);
        $message = SmsTemplate::orderPay($link);

        Yii::$app->queue->delay(10)->push(new SmsJob([
            'phone' => $order->phone,
            'message' => $message
        ]));
    }
}