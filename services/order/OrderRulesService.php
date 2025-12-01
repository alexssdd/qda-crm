<?php

namespace app\services\order;

use Yii;
use DomainException;
use app\entities\Order;
use app\entities\OrderReceipt;
use app\core\helpers\UserHelper;
use app\core\helpers\OrderHelper;
use app\entities\OrderStoreProduct;
use app\core\helpers\OrderStoreHelper;

/**
 * Order rules service
 */
class OrderRulesService
{
    private $_order;

    public function __construct(Order $order)
    {
        $this->_order = $order;
    }

    public function hasReceipt(): void
    {
        if (OrderReceipt::find()->andWhere(['order_id' => $this->_order->id])->exists()) {
            throw new DomainException('Сборку удалить нельзя — в заказе уже есть чек продажи.');
        }
    }

    /**
     * @param $prevStatus
     * @param $newStatus
     * @return void
     * @property OrderStoreProduct $orderStoreProduct
     */
    public function hasAssembly($prevStatus, $newStatus): void
    {
        $order = $this->_order;

        // Check role
        if (UserHelper::isAdmin()){
            return;
        }

        // Check status
        if ($prevStatus !== OrderHelper::STATUS_ACCEPTED && !in_array($newStatus, [OrderHelper::STATUS_SHIPPED, OrderHelper::STATUS_PICKUP])) {
            return;
        }

        // Check assembly
        foreach ($order->products as $orderProduct) {
            if ($orderProduct->quantity <= 0) {
                continue;
            }

            $assemblyQuantity = 0;
            foreach ($orderProduct->orderStoreProducts as $orderStoreProduct) {
                if ($orderStoreProduct->hasQuantity()) {
                    $assemblyQuantity += floor($orderStoreProduct->quantity);
                }

                if ($orderStoreProduct->orderStore->status !== OrderStoreHelper::STATUS_ASSEMBLED) {
                    throw new DomainException(Yii::t('order', 'Order store status not assembled'));
                }
            }

            if ($assemblyQuantity < $orderProduct->quantity || $assemblyQuantity > $orderProduct->quantity) {
                $message = Yii::t(
                    'order',
                    'Incorrect quantity {product}, required: {required}, current: {current}',
                    [
                        'product' => $orderProduct->name,
                        'required' => $orderProduct->quantity,
                        'current' => $assemblyQuantity
                    ]
                );

                throw new DomainException($message);
            }
        }
    }

    /**
     * @return void
     */
    public function notAllowedAssembly(): void
    {
        $notAllowedStatuses = [
            OrderHelper::STATUS_SHIPPED,
            OrderHelper::STATUS_PICKUP,
            OrderHelper::STATUS_DELIVERED,
            OrderHelper::STATUS_ISSUED,
            OrderHelper::STATUS_CANCELLED,
        ];

        if (in_array($this->_order->status, $notAllowedStatuses)) {
            throw new DomainException(Yii::t('order', 'Assembly is not allowed in this status'));
        }
    }
}