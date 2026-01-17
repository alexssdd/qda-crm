<?php

namespace app\services\order;

use DomainException;
use app\entities\User;
use app\entities\Order;

/**
 * Order assign service
 */
class OrderAssignService
{
    private $_order;
    private $_user;

    /**
     * OrderBotService constructor.
     * @param Order $order
     * @param User $user
     */
    public function __construct(Order $order, User $user)
    {
        $this->_order = $order;
        $this->_user = $user;
    }

    /**
     * Assign
     */
    public function assign()
    {
        $order = $this->_order;
        $user = $this->_user;
        $order->executor_id = $user->id;

        if ($order->created_by){
            $order->handler_id = $order->created_by;
        } else {
            $order->handler_id = $user->id;
        }

        if (!$order->save(false)) {
            throw new DomainException("Order id: $order->id, executor set error");
        }
    }
}