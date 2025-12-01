<?php

namespace app\services\order;

use Exception;
use DomainException;
use app\entities\Order;
use app\core\helpers\OrderHelper;
use app\forms\order\OrderUpdateForm;
use app\forms\order\OrderTransferForm;

/**
 * Order manage service
 */
class OrderManageService
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
     * @param OrderUpdateForm $form
     * @return void
     * @throws Exception
     */
    public function update(OrderUpdateForm $form): void
    {
        $order = $this->_order;
        $order->status = $form->status;

        if (in_array($order->status, [OrderHelper::STATUS_ISSUED, OrderHelper::STATUS_DELIVERED])) {
            $order->completed_at = time();
        }

        if (!$order->save(false)) {
            throw new DomainException("Order id: $order->id, save error");
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function accept(): void
    {
        $order = $this->_order;
        $order->status = OrderHelper::STATUS_ACCEPTED;

        if (!$order->save(false)) {
            throw new DomainException("Order id: $order->id, accept error");
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function pickup(): void
    {
        $order = $this->_order;

        if ($order->status == OrderHelper::STATUS_PICKUP) {
            return;
        }

        $order->status = OrderHelper::STATUS_PICKUP;

        if (!$order->save(false)) {
            throw new DomainException("Order id: $order->id, set status pickup error");
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function issued(): void
    {
        $order = $this->_order;
        if ($order->status == OrderHelper::STATUS_ISSUED) {
            return;
        }

        $order->completed_at = time();
        $order->status = OrderHelper::STATUS_ISSUED;

        if (!$order->save(false)) {
            throw new DomainException("Order id: $order->id, set status issued error");
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function shipped(): void
    {
        $order = $this->_order;

        if ($order->status == OrderHelper::STATUS_SHIPPED) {
            return;
        }

        $order->status = OrderHelper::STATUS_SHIPPED;

        if (!$order->save(false)) {
            throw new DomainException("Order id: $order->id, set status shipped error");
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function courier(): void
    {
        $order = $this->_order;

        if ($order->status == OrderHelper::STATUS_COURIER) {
            return;
        }

        $order->status = OrderHelper::STATUS_COURIER;

        if (!$order->save(false)) {
            throw new DomainException("Order id: $order->id, set status courier error");
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function delivered(): void
    {
        $order = $this->_order;

        if ($order->status == OrderHelper::STATUS_DELIVERED) {
            return;
        }

        $order->status = OrderHelper::STATUS_DELIVERED;
        $order->completed_at = time();

        if (!$order->save(false)) {
            throw new DomainException("Order id: $order->id, set status delivered error");
        }
    }

    /**
     * @param $reason
     * @return void
     * @throws Exception
     */
    public function cancelled($reason = null): void
    {
        $order = $this->_order;
        $order->status = OrderHelper::STATUS_CANCELLED;
        $order->completed_at = time();

        if (!$order->save(false)) {
            throw new DomainException("Order id: $order->id, set status cancel error");
        }
    }

    /**
     * @param OrderTransferForm $form
     * @return void
     * @throws Exception
     */
    public function transfer(OrderTransferForm $form): void
    {
        $order = $this->_order;
        $order->executor_id = $form->executor_id;

        if (!$order->save(false)) {
            throw new DomainException("Order id: $order->id, transfer error");
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function setPending(): void
    {
        $order = $this->_order;
        $extraFields = $order->extra_fields;

        // Set pending
        $extraFields['is_pending'] = true;
        $order->extra_fields = $extraFields;

        if (!$order->save(false)) {
            throw new DomainException("Order id: $order->id, transfer error");
        }
    }
}