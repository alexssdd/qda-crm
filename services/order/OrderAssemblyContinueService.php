<?php

namespace app\services\order;

use Yii;
use Exception;
use Throwable;
use DOMException;
use DomainException;
use app\entities\User;
use app\entities\Order;
use app\entities\OrderStore;
use app\entities\OrderReserve;
use app\services\ConsoleService;
use app\core\helpers\OrderHelper;
use app\core\helpers\StoreHelper;
use app\core\helpers\PaymentHelper;
use app\core\helpers\DeliveryHelper;
use app\core\helpers\OrderEventHelper;
use app\modules\stock\services\ZnpService;
use app\modules\wb\jobs\WbOrderTransitJob;
use app\modules\ozon\jobs\OzonOrderWaybillJob;
use app\modules\kaspi\jobs\KaspiOrderAssembledJob;
use app\modules\picker\jobs\PickerAssemblyNotifyJob;

/**
 * Order assembly continue service
 */
class OrderAssemblyContinueService
{
    private $_order;
    private $_user;
    private $_storeId;
    private $_orderStore;

    /**
     * @param Order $order
     * @param User $user
     * @param $storeId
     */
    public function __construct(Order $order, User $user, $storeId)
    {
        $this->_order = $order;
        $this->_user  = $user;
        $this->_storeId = $storeId;
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function run(): void
    {
        $order = $this->_order;

        if (OrderHelper::isChannelMobile($order)) {
            (new ConsoleService())->runApi('mobile/order/handle-after-assembly', [$order->id]);
            return;
        }

        if (OrderHelper::isChannelSite($order)) {
            (new ConsoleService())->runApi('site/order/handle-after-assembly', [$order->id]);
            return;
        }

        if ($order->channel == OrderHelper::CHANNEL_KASPI_SHOP) {
            (new ConsoleService())->runApi('kaspi/order/handle-after-assembly', [$order->id]);
            return;
        }

        switch ($order->delivery_method) {
            case DeliveryHelper::DELIVERY_WOLT:
                $this->handleWolt();
                return;
            case DeliveryHelper::DELIVERY_GLOVO:
                $this->handleGlovo();
                return;
            case DeliveryHelper::DELIVERY_YANDEX_EDA:
                $this->handleYandexEda();
                return;
            case DeliveryHelper::DELIVERY_OZON:
                $this->runOzon();
                break;
            case DeliveryHelper::DELIVERY_WB:
                // Поставим транзит-джобу и не блокируем поток
                Yii::$app->queue->delay(60)->push(new WbOrderTransitJob([
                    'order_id' => $order->id,
                ]));
                break;
            // Остальные методы — уведомляем сборщика: по конкретному store либо по всем OrderStore
            default:
                // Picker notify
                $this->pickerNotify();
                break;
        }
    }

    protected function handleYandexEda(): void
    {
        try {
            // Assembled Store
            $orderStore = $this->getOrderStore();

            // Picker
            Yii::$app->queue->push(new PickerAssemblyNotifyJob([
                'order_id' => $this->_order->id,
                'store_id' => $orderStore->store_id,
            ]));
        } catch (Exception $e) {
            $this->createEvent($e->getMessage());
            throw $e;
        }
    }

    protected function handleGlovo(): void
    {
        try {
            // Assembled Store
            $orderStore = $this->getOrderStore();

            // Picker
            Yii::$app->queue->push(new PickerAssemblyNotifyJob([
                'order_id' => $this->_order->id,
                'store_id' => $orderStore->store_id,
            ]));
        } catch (Exception $e) {
            $this->createEvent($e->getMessage());
            throw $e;
        }
    }

    protected function handleWolt(): void
    {
        try {
            // Assembled Store
            $orderStore = $this->getOrderStore();

            // Picker
            Yii::$app->queue->push(new PickerAssemblyNotifyJob([
                'order_id' => $this->_order->id,
                'store_id' => $orderStore->store_id,
            ]));
        } catch (Exception $e) {
            $this->createEvent($e->getMessage());
            throw $e;
        }
    }

    /**
     * @return void
     * @throws Throwable
     */
    private function runOzon(): void
    {
        try {
            $orderStore = $this->getOrderStore();

            if ($orderStore->store->type == StoreHelper::TYPE_SHOP) {
                Yii::$app->queue->push(new PickerAssemblyNotifyJob([
                    'order_id' => $this->_order->id,
                    'store_id' => $orderStore->store_id,
                ]));
                return; // wait response picker and continue
            }
            // Continue only store type

            // Znp
            (new ZnpService($orderStore))->create();

            // Ship
            (new ConsoleService())->runApi('ozon/order/ship', [$orderStore->id]);

            // Waybill delay 60s
            Yii::$app->queue->delay(60)->push(new OzonOrderWaybillJob([
                'order_id' => $orderStore->order_id
            ]));
        } catch (Throwable $e) {
            $this->createEvent($e->getMessage());
            throw $e;
        }
    }

    /**
     * @return void
     * @throws DOMException
     */
    private function pickerNotify(): void
    {
        $orderStore = $this->getOrderStore();
        $store = $orderStore->store;

        if ($store->type == StoreHelper::TYPE_STORE){
            (new ConsoleService())->run('stock/znp/create', [$this->_order->id, $store->id]);
        } else {
            (new ZnpService($orderStore))->createForShop();
            Yii::$app->queue->push(new PickerAssemblyNotifyJob([
                'order_id' => $this->_order->id,
                'store_id' => $store->id,
            ]));
        }
    }

    /**
     * @return void
     */
    protected function kaspiStoreAssembled(): void
    {
        $order = $this->_order;

        if ($order->channel !== OrderHelper::CHANNEL_KASPI_SHOP){
            return;
        }

        if ($order->store && $order->store->type !== StoreHelper::TYPE_STORE){
            return;
        }

        Yii::$app->queue->delay(60)->push(new KaspiOrderAssembledJob(['order_id' => $order->id]));
    }

    /**
     * @param $message
     * @param int $type
     * @param array $data
     * @return void
     * @throws Exception
     */
    protected function createEvent($message, int $type = OrderEventHelper::TYPE_ASSEMBLY_ERROR, array $data = []): void
    {
        (new OrderEventService($this->_order, $this->_user))->create($message, $type, $data);
    }

    /**
     * @return OrderStore|null
     */
    private function getOrderStore(): ?OrderStore
    {
        if ($this->_orderStore === null) {
            $model = OrderStore::findOne([
                'order_id' => $this->_order->id,
                'store_id' => $this->_storeId,
            ]);
            if (!$model) {
                throw new DomainException('Order store not found');
            }
            $this->_orderStore = $model;
        }
        return $this->_orderStore;
    }
}