<?php

namespace app\services\order;

use Exception;
use DomainException;
use app\entities\User;
use app\entities\Order;
use app\entities\OrderReason;
use app\forms\order\OrderCancelForm;
use app\core\helpers\OrderReasonHelper;

/**
 * Order reason service
 */
class OrderReasonService
{
    private $_order;
    private $_user;

    /**
     * @param Order $order
     * @param User $user
     */
    public function __construct(Order $order, User $user)
    {
        $this->_order = $order;
        $this->_user = $user;
    }

    /**
     * @param OrderCancelForm $form
     * @return OrderReason
     * @throws Exception
     */
    public function cancel(OrderCancelForm $form): OrderReason
    {
        $model = new OrderReason();
        $model->order_id = $this->_order->id;
        $model->user_id = $this->_user->id;
        $model->action = OrderReasonHelper::ACTION_CANCEL;
        $model->reason = $form->reason;
        $model->reason_additional = $form->reason_additional;
        $model->text = $form->text;
        $model->created_at = time();

        if (!$model->save()){
            throw new DomainException($model->getErrorSummary(true)[0]);
        }

        return $model;
    }
}