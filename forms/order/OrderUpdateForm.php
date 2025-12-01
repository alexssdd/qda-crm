<?php

namespace app\forms\order;

use app\entities\Order;
use app\core\forms\Form;
use app\core\helpers\OrderHelper;

/**
 * Order update form
 */
class OrderUpdateForm extends Form
{
    public $status;

    private $_order;

    /**
     * @param Order $order
     * @param array $config
     */
    public function __construct(Order $order, array $config = [])
    {
        $this->_order = $order;
        $this->status = $order->status;

        parent::__construct($config);
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            ['status', 'in', 'range' => array_keys(OrderHelper::getAvailableStatuses($this->_order))]
        ];
    }
}