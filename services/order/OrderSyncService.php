<?php

namespace app\services\order;

use Yii;
use Exception;
use DomainException;
use app\entities\User;
use app\entities\Order;
use app\services\ConsoleService;
use app\core\helpers\OrderHelper;
use app\forms\order\OrderUpdateForm;
use app\modules\delivery\jobs\DeliveryOrderCancelJob;
use app\modules\delivery\jobs\DeliveryOrderCreateJob;
use app\modules\delivery\jobs\DeliveryOrderDeliveredJob;

/**
 * Order sync service
 */
class OrderSyncService
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
     * @param OrderUpdateForm $form
     * @return void
     * @throws Exception
     */
    public function update(OrderUpdateForm $form): void
    {
        $order = $this->_order;
        $order->status = $form->status;

        if (!$order->save(false)) {
            throw new DomainException("Order id: $order->id, save error");
        }
    }

    /**
     * @param $prevStatus
     * @return void
     * @throws Exception
     */
    public function run($prevStatus): void
    {
        $order = $this->_order;

        // no changed status
        if ($prevStatus == $order->status) {
            return;
        }

        switch ($order->status) {
            case OrderHelper::STATUS_SHIPPED:
                $this->shipped();
                break;
            case OrderHelper::STATUS_DELIVERED:
                $this->delivered();
                break;
            case OrderHelper::STATUS_PICKUP:
                $this->ready();
                break;
            case OrderHelper::STATUS_ISSUED:
                $this->issued();
                break;
            case OrderHelper::STATUS_CANCELLED:
                $this->cancel();
                break;
        }
    }

    public function accept()
    {

    }

    /**
     * @return void
     * @throws Exception
     */
    public function shipped(): void
    {
        $order = $this->_order;

        // Delivery: create order
        Yii::$app->queue->push(new DeliveryOrderCreateJob([
            'order_id' => $order->id
        ]));

        // EPS
        (new ConsoleService())->runApi('ax/eps/order-handled', [$order->id]);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function delivered(): void
    {
        $order = $this->_order;

        // Delivery: delivered order
        Yii::$app->queue->push(new DeliveryOrderDeliveredJob([
            'order_id' => $order->id
        ]));
    }

    /**
     * @return void
     */
    public function ready(): void
    {
        $order = $this->_order;

        // EPS
        (new ConsoleService())->runApi('ax/eps/order-handled', [$order->id]);
    }

    /**
     * @return void
     */
    public function issued(): void
    {
        $order = $this->_order;
        if ($order->channel !== OrderHelper::CHANNEL_MARKET) {
            return;
        }
        // Yii::$app->queue->delay(10)->push(new OrderIssuedJob(['id' => $order->id]));
    }

    /**
     * @return void
     * @throws Exception
     */
    public function cancel(): void
    {
        $order = $this->_order;

        switch ($order->channel) {
            case OrderHelper::CHANNEL_KASPI_SHOP:
                (new ConsoleService())->runApi('kaspi/order/cancel', [$order->id]);
                break;
            case OrderHelper::CHANNEL_SITE_MELOMAN:
            case OrderHelper::CHANNEL_SITE_MARWIN:
                (new ConsoleService())->runApi('site/order/cancel', [$order->id]);
                break;
            case OrderHelper::CHANNEL_WOLT:
                (new ConsoleService())->runApi('wolt/order/cancel', [$order->id]);
                break;
            case OrderHelper::CHANNEL_JUSAN:
                (new ConsoleService())->run('jusan/order/cancel', [$order->id]);
                break;
            case OrderHelper::CHANNEL_OZON:
                (new ConsoleService())->runApi('ozon/order/cancel', [$order->id]);
                break;
            case OrderHelper::CHANNEL_WB:
                (new ConsoleService())->run('wb/order/cancel', [$order->id]);
                break;
            case OrderHelper::CHANNEL_FORTE:
                (new ConsoleService())->run('forte/order/cancel', [$order->id]);
                break;
            case OrderHelper::CHANNEL_APP_IOS:
            case OrderHelper::CHANNEL_APP_ANDROID:
                (new ConsoleService())->runApi('mobile/order/cancel', [$order->id]);
                break;
        }
    }
}