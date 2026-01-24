<?php

namespace app\services\order;

use DomainException;
use app\modules\auth\models\User;
use app\modules\order\models\Order;
use app\modules\order\models\OrderHistory;

/**
 * Order history service
 */
class OrderHistoryService
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
     * @param $statusBefore
     * @param $statusAfter
     * @return void
     */
    public function create($statusBefore, $statusAfter)
    {
        if ($statusBefore == $statusAfter){
            return;
        }

        $model = new OrderHistory();
        $model->order_id = $this->_order->id;
        $model->status_before = $statusBefore;
        $model->status_after = $statusAfter;
        $model->created_at = time();
        $model->created_by = $this->_user->id;

        if (!$model->save()){
            throw new DomainException($model->getErrorSummary(true)[0]);
        }
    }
}