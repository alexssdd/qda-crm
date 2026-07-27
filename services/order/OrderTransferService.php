<?php

namespace app\services\order;

use Throwable;
use DomainException;
use app\modules\auth\models\User;
use app\modules\auth\helpers\UserHelper;
use app\modules\order\models\Order;
use app\modules\order\helpers\OrderEventHelper;
use app\modules\order\helpers\OrderHelper;

class OrderTransferService
{
    public function __construct(
        private readonly Order $order,
        private readonly User $actor,
    ) {
    }

    public function transfer(User $operator): void
    {
        if (!OrderHelper::canTransfer($this->order->status)) {
            throw new DomainException('Заказ на текущей стадии нельзя передать оператору');
        }

        if (
            $operator->role !== UserHelper::ROLE_OPERATOR
            || (int) $operator->status !== UserHelper::STATUS_ACTIVE
        ) {
            throw new DomainException('Выбранный оператор недоступен');
        }

        if (!$this->order->hasAttribute('handler_id')) {
            throw new DomainException('Заказ не поддерживает назначение ответственного');
        }

        $transaction = Order::getDb()->beginTransaction();

        try {
            $this->order->handler_id = $operator->id;

            if ($this->order->hasAttribute('executor_id')) {
                $this->order->executor_id = $operator->id;
            }

            if (!$this->order->save(false)) {
                throw new DomainException('Не удалось назначить оператора');
            }

            $event = (new OrderEventService($this->order, $this->actor))->create(
                'Заказ передан оператору: ' . $operator->name,
                OrderEventHelper::TYPE_TRANSFER,
            );

            if ($event === null) {
                throw new DomainException('Не удалось записать передачу в историю заказа');
            }

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}
