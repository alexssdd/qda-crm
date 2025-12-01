<?php

namespace app\commands;

use Exception;
use app\entities\Order;
use yii\console\Controller;
use app\entities\OrderStore;
use app\entities\OrderEvent;
use app\core\helpers\TextHelper;
use app\core\helpers\UserHelper;
use app\core\helpers\StoreHelper;
use app\core\helpers\OrderHelper;
use app\core\helpers\DeliveryHelper;
use app\core\helpers\OrderStoreHelper;
use app\core\helpers\OrderEventHelper;
use app\services\order\OrderBotService;
use app\services\order\OrderEventService;

/**
 * Service controller
 */
class ServiceController extends Controller
{
    /**
     * @return void
     * @throws Exception
     */
    public function actionOrderTransferError()
    {
        $user = UserHelper::getBot();
        $orders = Order::find()
            ->alias('orders')
            ->leftJoin(['events' => OrderEvent::tableName()], implode(' AND ', [
                'orders.id = events.order_id',
                'events.type = ' . OrderEventHelper::TYPE_TRANSFER_ERROR
            ]))
            ->andWhere(['orders.status' => [OrderHelper::STATUS_NEW, OrderHelper::STATUS_ACCEPTED]])
            ->andWhere(['>=', 'orders.created_at', (time() - 60 * 60 * 24)])
            ->andWhere('orders.executor_id IS NULL')
            ->andWhere('events.id IS NOT NULL')
            ->groupBy(['orders.id'])
            ->all();

        foreach ($orders as $order) {
            (new OrderBotService($order, $user))->autoTransfer();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionOrderAssemblyNotify()
    {
        /** @var OrderStore[] $orderStores */
        $orderStores = OrderStore::find()
            ->alias('t1')
            ->joinWith(['order t2'])
            ->andWhere([
                't1.status' => OrderStoreHelper::STATUS_NEW,
                't2.status' => OrderHelper::STATUS_ACCEPTED,
            ])
            ->all();

        $user = UserHelper::getBot();
        foreach ($orderStores as $orderStore) {
            $order = $orderStore->order;

            if ($order->delivery_method == DeliveryHelper::DELIVERY_EXPRESS && time() >= ($orderStore->created_at + 60 * 14) && time() <= ($orderStore->created_at + 60 * 19)) {
                (new OrderEventService($order, $user))->create(
                    TextHelper::orderAssemblyNotify(StoreHelper::getNameShort($orderStore->store), 15),
                    OrderEventHelper::TYPE_ASSEMBLY_NOTIFY,
                    ['store_id' => $orderStore->store_id]
                );
            }
        }
    }
}