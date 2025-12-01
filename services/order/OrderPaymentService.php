<?php

namespace app\services\order;

use app\entities\Order;
use DomainException;
use app\entities\OrderPayment;
use app\core\helpers\PaymentHelper;

class OrderPaymentService
{
    private $_order;

    public function __construct(Order $order)
    {
        $this->_order = $order;
    }

    public function create($type, $provider, $providerId = null, $cost = 0, $status = PaymentHelper::STATUS_NEW): OrderPayment
    {
        $payment = new OrderPayment();
        $payment->order_id = $this->_order->id;
        $payment->type = $type;
        $payment->provider = $provider;
        $payment->provider_id = $providerId;
        $payment->provider_cost = $cost;
        $payment->status = $status;
        $payment->created_at = time();

        if (!$payment->save(false)) {
            throw new DomainException('Failed to create payment record');
        }

        return $payment;
    }
}