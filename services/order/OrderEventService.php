<?php

namespace app\services\order;

use Exception;
use DomainException;
use app\modules\auth\models\User;
use app\modules\order\models\Order;
use app\modules\order\models\OrderEvent;
use app\modules\order\models\OrderHistory;

/**
 * Order event service
 */
class OrderEventService
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
     * @param string $message
     * @param $type
     * @param array $data
     * @return OrderEvent|null
     * @throws Exception
     */
    public function create(string $message = '', $type = null, array $data = []): ?OrderEvent
    {
        $history = $this->getLastHistory();
        if (!$history){
            return null;
        }

        $model = new OrderEvent();
        $model->order_id = $this->_order->id;
        $model->history_id = $history->id;
        $model->type = $type;
        $model->message = $message;
        $model->data = $data;
        $model->created_at = time();
        $model->created_by = $this->_user->id;

        if (!$model->save()){
            throw new DomainException($model->getErrorSummary(true)[0]);
        }

        return $model;
    }

    protected function getLastHistory(): ?OrderHistory
    {
        return OrderHistory::find()
            ->andWhere(['order_id' => $this->_order->id])
            ->orderBy(['id' => SORT_DESC])
            ->limit(1)
            ->one();
    }
}