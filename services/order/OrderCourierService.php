<?php

namespace app\services\order;

use Exception;
use DomainException;
use app\entities\Order;
use app\entities\OrderCourier;

/**
 * Order courier service
 */
class OrderCourierService
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
     * @param $name
     * @param $phone
     * @param $arrivalAt
     * @return void
     * @throws Exception
     */
    public function create($name, $phone, $arrivalAt)
    {
        $order = $this->_order;

        if (!$model = $order->courier) {
            $model = new OrderCourier();
            $model->order_id = $this->_order->id;
        }

        $model->name = $name;
        $model->phone = $phone;
        $model->extra_fields = [
            'arrival_at' => $arrivalAt
        ];

        if (!$model->save(false)) {
            throw new DomainException($model->getErrorMessage());
        }
    }
}