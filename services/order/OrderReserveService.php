<?php

namespace app\services\order;

use Exception;
use DomainException;
use app\services\LogService;
use app\entities\OrderStore;
use app\entities\OrderReserve;
use app\core\helpers\LogHelper;
use app\core\helpers\OrderReserveHelper;

class OrderReserveService
{
    private $_orderStore;

    public function __construct(OrderStore $orderStore)
    {
        $this->_orderStore = $orderStore;
    }

    public function createFromZnp(string $locationFrom, string $locationTo, $transferId = null): void
    {
        $target = LogHelper::TARGET_ORDER_RESERVE_CREATE;

        try {
            $reserve = new OrderReserve();
            $reserve->order_id = $this->_orderStore->order_id;
            $reserve->store_id = $this->_orderStore->store_id;
            $reserve->number = $transferId;
            $reserve->location_from = $locationFrom;
            $reserve->location_to = $locationTo;
            $reserve->created_at = time();
            $reserve->status = OrderReserveHelper::STATUS_ACTIVE;

            if (!$reserve->save(false)) {
                throw new DomainException("Unable create order reserve");
            }

            LogService::success($target, ['number' => $this->_orderStore->order->number, 'znp' => $transferId]);
        } catch (Exception $e) {
            LogService::error($target, ['number' => $this->_orderStore->order->number, 'znp' => $transferId, 'error' => $e->getMessage()]);
        }
    }
}