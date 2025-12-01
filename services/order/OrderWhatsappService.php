<?php

namespace app\services\order;

use Exception;
use app\entities\User;
use app\entities\Order;
use app\entities\OrderStore;
use yii\web\NotFoundHttpException;
use app\core\helpers\NotifyHelper;
use app\core\helpers\OrderEventHelper;
use app\core\helpers\OrderStoreHelper;
use app\forms\order\OrderWhatsappForm;
use app\core\helpers\OrderNotifyHelper;
use app\modules\edna\helpers\EdnaHelper;
use app\modules\edna\services\EdnaService;

/**
 * Order whatsapp service
 */
class OrderWhatsappService
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
     * @param OrderWhatsappForm $form
     * @return void
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function send(OrderWhatsappForm $form): void
    {
        if ($form->template == OrderNotifyHelper::WHATSAPP_PICKUP_READY){
            $this->pickupReady();
        }
        if ($form->template == OrderNotifyHelper::WHATSAPP_CANCELLED){
            $this->cancelled();
        }
    }

    /**
     * @return void
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function pickupReady(): void
    {
        $order = $this->_order;
        $user = $this->_user;
        $orderStore = $this->getOrderStore();

        if (!$orderStore){
            throw new NotFoundHttpException('Сборка не найдена');
        }

        // Variables
        $storeName = $orderStore->store ? $orderStore->store->name : 'Undefined store';
        $number = $order->vendor_number ?: $order->number;
        $number = (string)$number;

        // Send messages
        (new EdnaService())->sendMessage([
            'sender' => EdnaHelper::SENDER_MARWIN,
            'phone' => $order->phone,
            'templateId' => EdnaHelper::TEMPLATE_ORDER_READY_NOTIFICATION,
            'textVariables' => [$number, $storeName],
        ]);

        // Event
        (new OrderEventService($order, $user))->create('', OrderEventHelper::TYPE_NOTIFY_PICKUP_READY, [
            'provider' => NotifyHelper::PROVIDER_WHATSAPP,
            'lang' => 'ru',
            'store_name' => $storeName,
            'number' => $number
        ]);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function cancelled(): void
    {
        $order = $this->_order;
        $user = $this->_user;

        // Variables
        $number = $order->vendor_number ?: $order->number;
        $number = (string)$number;

        // Send messages
        (new EdnaService())->sendMessage([
            'sender' => EdnaHelper::SENDER_MARWIN,
            'phone' => $order->phone,
            'templateId' => EdnaHelper::TEMPLATE_ORDER_CANCELLATION,
            'textVariables' => [$number],
        ]);

        // Event
        (new OrderEventService($order, $user))->create('', OrderEventHelper::TYPE_NOTIFY_CANCELLED, [
            'provider' => NotifyHelper::PROVIDER_WHATSAPP,
            'number' => $number
        ]);
    }

    /**
     * @return OrderStore|null
     */
    protected function getOrderStore(): ?OrderStore
    {
        return OrderStore::findOne([
            'order_id' => $this->_order->id,
            'status' => [OrderStoreHelper::STATUS_ASSEMBLED, OrderStoreHelper::STATUS_COMPLETE]
        ]);
    }
}