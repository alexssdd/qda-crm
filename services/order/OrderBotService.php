<?php

namespace app\services\order;

use Yii;
use Throwable;
use Exception;
use DomainException;
use app\entities\User;
use app\entities\Store;
use app\entities\Order;
use yii\helpers\ArrayHelper;
use app\entities\OrderStore;
use app\services\LogService;
use app\entities\OrderPayment;
use app\core\helpers\LogHelper;
use app\services\ConsoleService;
use yii\db\StaleObjectException;
use app\core\helpers\UserHelper;
use app\core\helpers\TextHelper;
use app\core\helpers\StockHelper;
use app\core\helpers\StoreHelper;
use app\core\helpers\OrderHelper;
use app\services\OperatorService;
use app\services\StockSortService;
use app\core\helpers\PaymentHelper;
use app\core\helpers\DeliveryHelper;
use app\core\helpers\OrderEventHelper;
use app\core\exceptions\BotEventException;
use app\modules\wb\jobs\WbOrderTransitJob;
use app\modules\stock\services\ZnpService;
use app\modules\ozon\jobs\OzonOrderWaybillJob;
use app\modules\kaspi\jobs\KaspiOrderAssembledJob;
use app\modules\picker\jobs\PickerAssemblyNotifyJob;
use app\modules\stock\services\StockDistanceService;

/**
 * Order bot service
 */
class OrderBotService
{
    private $_order;
    private $_user;
    private $_logs = [];

    /**
     * @param Order $order
     * @param User|null $user
     * @throws Exception
     */
    public function __construct(Order $order, User $user = null)
    {
        $this->_order = $order;
        $this->_user = $user;

        if (!$user) {
            $this->_user = UserHelper::getBot();
        }
    }

    /**
     * @return void
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function handle(): void
    {
        // Для канала мобилка
        if (OrderHelper::isChannelMobile($this->_order)) {
            return; // handle in api modules mobile !!!
        }

        // Для канала сайт
        if (OrderHelper::isChannelSite($this->_order)) {
            return; // handle in api modules site !!!
            match ($this->_order->delivery_method) {
                DeliveryHelper::DELIVERY_PICKUP => $this->handleSitePickup(),
                DeliveryHelper::DELIVERY_EXPRESS => $this->handleSiteExpress(),
                DeliveryHelper::DELIVERY_EMEX => $this->handleSiteEmex(),
            };
            return;
        }

		switch ($this->_order->delivery_method) {
            case DeliveryHelper::DELIVERY_PICKUP:
                $this->handlePickup();
                break;
            case DeliveryHelper::DELIVERY_STANDARD:
                $this->handleDeliveryStandard();
                break;
            case DeliveryHelper::DELIVERY_EXPRESS:
                $this->handleDeliveryExpress();
                break;
            case DeliveryHelper::DELIVERY_KASPI:
                $this->handleDeliveryKaspi();
                break;
            case DeliveryHelper::DELIVERY_KASPI_EXPRESS:
                $this->handleDeliveryKaspiExpress();
                break;
            case DeliveryHelper::DELIVERY_WOLT:
                $this->handleWolt();
                break;
            case DeliveryHelper::DELIVERY_GLOVO:
                $this->handleGlovo();
                break;
            case DeliveryHelper::DELIVERY_YANDEX_EDA:
                $this->handleYandexEda();
                break;
            case DeliveryHelper::DELIVERY_JUSAN:
                $this->handleDeliveryJusan();
                break;
            case DeliveryHelper::DELIVERY_HALYK:
                $this->handleHalykExpress();
                break;
            case DeliveryHelper::DELIVERY_OZON:
                $this->handleOzon();
                break;
            case DeliveryHelper::DELIVERY_WB:
            case DeliveryHelper::DELIVERY_WB_EXPRESS:
            case DeliveryHelper::DELIVERY_WB_PICKUP:
                $this->handleDeliveryWb();
                break;
            case DeliveryHelper::DELIVERY_FORTE:
            case DeliveryHelper::DELIVERY_FORTE_EXPRESS:
                $this->handleDeliveryForte();
                break;
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function autoTransfer(): void
    {
        $order = $this->_order;

        try {
            // Get operator
            $operator = (new OperatorService())->getOperator();

            // Assign order
            (new OrderAssignService($order, $operator))->assign();

            // Create message
            $this->createEvent(TextHelper::transferOrder($operator->full_name), OrderEventHelper::TYPE_TRANSFER);
        } catch (Exception $e){
            // Create message
            $this->createEvent($e->getMessage(), OrderEventHelper::TYPE_TRANSFER_ERROR);
        }
    }

    /**
     * @return void
     * @throws Throwable
     */
    protected function handleSitePickup(): void
    {
        $order = $this->_order;
        $user = $this->_user;

        try {
            $this->checkStatusNew();
            $this->setStatusAccept();

            if (in_array($order->payment_method, [PaymentHelper::PAYMENT_HALYK_ONLINE, PaymentHelper::PAYMENT_KASPI_ONLINE, PaymentHelper::PAYMENT_BCK_ONLINE])) {
                if (!$this->isPaid()) {
                    $this->createEvent('', OrderEventHelper::TYPE_PAYMENT_WAIT);
                    return;
                }
            }

            if ($order->comment) {
                $this->transfer();
                return;
            }

            // Check store
            $this->checkStore();

            // Check stock
            $stockService = new OrderStockService($order);
            $availableStore = $stockService->availableStore($order->store);
            $isAvailableStore = $availableStore['available'];
            $changes = $availableStore['changes'];

            // todo added logic re assembly
            if (!$isAvailableStore) {
                throw new BotEventException(
                    "Не удалось выбрать точку для сборки",
                    $changes ? ['products' => $changes] : []
                );
            }

            // Create assembly
            $assemblyService = new OrderAssemblyService($order, $user);
            $assemblyService->assemblyStore($order->store);

            // Create event
            $eventService = new OrderEventService($this->_order, $this->_user);
            $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_CREATED, ['store_id' => $order->store_id, 'name' => StoreHelper::getNameShort($order->store)]);

            $orderStore = $this->getOrderStore($order->id, $order->store_id);
            (new ZnpService($orderStore))->createForShop();

            // Picker
            Yii::$app->queue->push(new PickerAssemblyNotifyJob([
                'order_id' => $this->_order->id,
                'store_id' => $order->store->id,
            ]));

            // Bonus distribute
            (new OrderBonusService($order, $user))->distribute();
        } catch (Exception $e) {
            $this->createEvent($e->getMessage());
            $this->transfer();
            throw $e;
        }
    }

    /**
     * @return void
     * @throws StaleObjectException
     * @throws Throwable
     */
    protected function handleSiteExpress(): void
    {
        $order = $this->_order;
        $user = $this->_user;
        $allowedPaymentMethods = [
            PaymentHelper::METHOD_EPS,
            PaymentHelper::PAYMENT_KASPI_ONLINE,
            PaymentHelper::PAYMENT_HALYK_ONLINE,
            PaymentHelper::PAYMENT_BCK_ONLINE
        ];

        try {
            $this->checkStatusNew();
            $this->setStatusAccept();
            $this->checkCoordinates();

            if ($order->comment) {
                $this->transfer();
                return;
            }

            // Check payment
            if (!in_array($order->payment_method, $allowedPaymentMethods)) {
                throw new DomainException("Способ оплаты не поддерживается для доставки");
            }

            if (in_array($order->payment_method, [PaymentHelper::PAYMENT_HALYK_ONLINE, PaymentHelper::PAYMENT_KASPI_ONLINE, PaymentHelper::PAYMENT_BCK_ONLINE])) {
                if (!$this->isPaid()) {
                    $this->createEvent('', OrderEventHelper::TYPE_PAYMENT_WAIT);
                    return;
                }
            }

            $assembledStores = [];
            if ($order->store){
                $assembledStores[] = $order->store;
            } else {
                // Get stores
                $stockService = new OrderStockService($order);
                $stores = $stockService->getStores();
                $this->_logs['stores'] = $stores;

                // Fill distance
                $storesWithDistance = (new StockDistanceService())->fill($order->lat, $order->lng, $stores);
                $this->_logs['storesWithDistance'] = $storesWithDistance;

                // Sort
                $stockSortService = new StockSortService($storesWithDistance, OrderHelper::getProducts($order));
                $store = $stockSortService->getStore();

                // Assembly
                if ($store){
                    $this->_logs['store'] = $store;

                    // Set assembled stores
                    $assembledStores[] = $this->getStore($store['id']);
                } else {
                    $stores = $stockSortService->getStores();
                    if (!$stores){
                        throw new DomainException('Не удалось выбрать точку для сборки');
                    }
                    $this->_logs['stores'] = $stores;

                    foreach ($stores as $store){
                        // Set assembled stores
                        $assembledStores[] = $this->getStore($store['id']);
                    }
                }
            }

            // Handle assemblies
            foreach ($assembledStores as $store){
                // Create assembly
                $assemblyService = new OrderAssemblyService($order, $user);
                $assemblyService->assemblyStore($store);

                // Create event
                $eventService = new OrderEventService($this->_order, $this->_user);
                $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_CREATED, ['store_id' => $store->id, 'name' => StoreHelper::getNameShort($store)]);

                // Notify picker
                $this->pickerNotify($store);
            }

            // Save log
            $this->saveLogs();
        } catch (Exception $e) {
            // Save log
            $this->saveLogs(false);

            // Event
            $this->createEvent($e->getMessage());

            // Transfer
            $this->transfer();

            throw $e;
        }
    }

    /**
     * @return void
     * @throws StaleObjectException
     * @throws Throwable
     */
    protected function handleSiteEmex(): void
    {
        $order = $this->_order;
        $user = $this->_user;

        try {
            $this->checkStatusNew();
            $this->setStatusAccept();
            $this->checkCoordinates();

            if ($order->comment) {
                $this->transfer();
                return;
            }

            if (in_array($order->payment_method, [PaymentHelper::PAYMENT_HALYK_ONLINE, PaymentHelper::PAYMENT_KASPI_ONLINE, PaymentHelper::PAYMENT_BCK_ONLINE])) {
                if (!$this->isPaid()) {
                    $this->createEvent('', OrderEventHelper::TYPE_PAYMENT_WAIT);
                    return;
                }
            }

            $assembledStores = [];
            if ($order->store){
                $assembledStores[] = $order->store;
            } else {
                // Get stores
                $stockService = new OrderStockService($order);
                $stores = $stockService->getAllStores();
                $this->_logs['stores'] = $stores;

                // Sort
                $stockSortService = new StockSortService($stores, OrderHelper::getProducts($order), StockHelper::SORT_TYPE_EMEX, $order->city_id);
                $stockStore = $stockSortService->getStore();
                if (!$stockStore){
                    throw new DomainException('Не удалось выбрать точку для сборки');
                }
                $store = $this->getStore($stockStore['id']);
                if (!$store){
                    throw new DomainException('Не удалось выбрать точку для сборки');
                }

                $assembledStores[] = $store;
            }

            foreach ($assembledStores as $store){
                $assemblyService = new OrderAssemblyService($order, $user);
                $assemblyService->assemblyStore($store);

                // Create event
                $eventService = new OrderEventService($this->_order, $this->_user);
                $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_CREATED, ['store_id' => $store->id, 'name' => StoreHelper::getNameShort($store)]);

                $orderStore = $this->getOrderStore($order->id, $store->id);

                // Znp store
                if ($store->type == StoreHelper::TYPE_STORE) {
                    (new ZnpService($orderStore))->create();
                    return;
                }

                // Znp shop
                (new ZnpService($orderStore))->createForShop();

                // Picker
                Yii::$app->queue->push(new PickerAssemblyNotifyJob([
                    'order_id' => $order->id,
                    'store_id' => $store->id,
                ]));
            }
        } catch (Exception $e) {
            // Save log
            $this->saveLogs(false);

            // Event
            $this->createEvent($e->getMessage());

            // Transfer
            $this->transfer();

            throw $e;
        }
    }

    /**
     * @return void
     * @throws Throwable
     */
    protected function handlePickupKaspi(): void
    {
        $order = $this->_order;
        $user = $this->_user;

        try {
            $this->checkStatusNew();
            $this->setStatusAccept();

            // Always check has payment
            if (!$this->hasPayment()) {
                return; // waiting payment callback
            }

            // Check store
            $this->checkStore();

            // Check signature
            if ($this->checkSignatureRequired()){
                throw new DomainException('Необходимо подписания кредита');
            }

            // Check stock
            $stockService = new OrderStockService($order);
            $availableStore = $stockService->availableStore($order->store);
            $isAvailableStore = $availableStore['available'];
            $changes = $availableStore['changes'];

            // todo added logic re assembly
            if (!$isAvailableStore) {
                throw new BotEventException(
                    "Не удалось выбрать точку для сборки",
                    $changes ? ['products' => $changes] : []
                );
            }

            // Create assembly
            $assemblyService = new OrderAssemblyService($order, $user);
            $assemblyService->assemblyStore($order->store);

            // Create event
            $eventService = new OrderEventService($this->_order, $this->_user);
            $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_CREATED, ['store_id' => $order->store_id, 'name' => StoreHelper::getNameShort($order->store)]);

            $store = $order->store;
            $orderStore = $this->getOrderStore($order->id, $order->store_id);

            // Znp and picker
            if ($store->type == StoreHelper::TYPE_STORE){
                (new ZnpService($orderStore))->create();
            } else {
                (new ZnpService($orderStore))->createForShop();
                Yii::$app->queue->push(new PickerAssemblyNotifyJob([
                    'order_id' => $this->_order->id,
                    'store_id' => $store->id,
                ]));
            }

            // Kaspi store assembled
            $this->kaspiStoreAssembled();
        } catch (BotEventException $e) {
            $this->createEvent($e->getMessage(), OrderEventHelper::TYPE_ASSEMBLY_ERROR, $e->getData());
            $this->transfer();
            throw $e;
        } catch (Exception $e) {
            $this->createEvent($e->getMessage());
            $this->transfer();
            throw $e;
        }
    }

    /**
     * @return void
     * @throws BotEventException
     * @throws StaleObjectException
     * @throws Throwable
     */
    protected function handleOzon(): void
    {
        $order = $this->_order;
        $user = $this->_user;

        try {
            $this->checkStatusNew();
            $this->setStatusAccept();

            // Check store
            $this->checkStore();

            // Check stock
            $stockService = new OrderStockService($order);
            $availableStore = $stockService->availableStore($order->store);
            $isAvailableStore = $availableStore['available'];
            $changes = $availableStore['changes'];

            // todo added logic re assembly
            if (!$isAvailableStore) {
                throw new BotEventException(
                    "Не удалось выбрать точку для сборки",
                    $changes ? ['products' => $changes] : []
                );
            }

            // Create assembly
            $assemblyService = new OrderAssemblyService($order, $user);
            $assemblyService->assemblyStore($order->store);

            // Create event
            $eventService = new OrderEventService($this->_order, $this->_user);
            $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_CREATED, ['store_id' => $order->store_id, 'name' => StoreHelper::getNameShort($order->store)]);

            // Order store
            $store = $order->store;
            $orderStore = $this->getOrderStore($order->id, $order->store_id);

            if ($store->type == StoreHelper::TYPE_STORE) {
                (new ZnpService($orderStore))->create();
            } else {
                (new ZnpService($orderStore))->createForShop();
                Yii::$app->queue->push(new PickerAssemblyNotifyJob([
                    'order_id' => $this->_order->id,
                    'store_id' => $order->store_id,
                ]));
            }

            // Ship
            (new ConsoleService())->runApi('ozon/order/ship', [$orderStore->id]);

            // Waybill delay 60s
            Yii::$app->queue->delay(60)->push(new OzonOrderWaybillJob(['order_id' => $order->id]));
        } catch (BotEventException $e) {
            $this->createEvent($e->getMessage(), OrderEventHelper::TYPE_ASSEMBLY_ERROR, $e->getData());
            $this->transfer();
            throw $e;
        } catch (Exception $e) {
            $this->createEvent($e->getMessage());
            $this->transfer();
            throw $e;
        }
    }

    /**
     * @return void
     * @throws BotEventException
     * @throws StaleObjectException
     * @throws Throwable
     */
    protected function handleGlovo(): void
    {
        $order = $this->_order;
        $user = $this->_user;

        try {
            $this->checkStatusNew();
            $this->setStatusAccept();

            // Check store
            $this->checkStore();

            // Check stock
            $stockService = new OrderStockService($order);
            $availableStore = $stockService->availableStore($order->store);
            $isAvailableStore = $availableStore['available'];
            $changes = $availableStore['changes'];

            // todo added logic re assembly
            if (!$isAvailableStore) {
                throw new BotEventException(
                    "Не удалось выбрать точку для сборки",
                    $changes ? ['products' => $changes] : []
                );
            }

            // Create assembly
            $assemblyService = new OrderAssemblyService($order, $user);
            $assemblyService->assemblyStore($order->store);

            // Create event
            $eventService = new OrderEventService($this->_order, $this->_user);
            $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_CREATED, ['store_id' => $order->store_id, 'name' => StoreHelper::getNameShort($order->store)]);

            // Notify picker
            $this->pickerNotify($order->store);
        } catch (BotEventException $e) {
            $this->createEvent($e->getMessage(), OrderEventHelper::TYPE_ASSEMBLY_ERROR, $e->getData());
            $this->transfer();
            throw $e;
        } catch (Exception $e) {
            $this->createEvent($e->getMessage());
            $this->transfer();
            throw $e;
        }
    }

    /**
     * @return void
     * @throws BotEventException
     * @throws StaleObjectException
     * @throws Throwable
     */
    protected function handleWolt(): void
    {
        $order = $this->_order;
        $user = $this->_user;

        try {
            $this->checkStatusNew();
            $this->setStatusAccept();

            // Check store
            $this->checkStore();

            // Check stock
            $stockService = new OrderStockService($order);
            $availableStore = $stockService->availableStore($order->store);
            $isAvailableStore = $availableStore['available'];
            $changes = $availableStore['changes'];

            // todo added logic re assembly
            if (!$isAvailableStore) {
                throw new BotEventException(
                    "Не удалось выбрать точку для сборки",
                    $changes ? ['products' => $changes] : []
                );
            }

            // Create assembly
            $assemblyService = new OrderAssemblyService($order, $user);
            $assemblyService->assemblyStore($order->store);

            // Create event
            $eventService = new OrderEventService($this->_order, $this->_user);
            $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_CREATED, ['store_id' => $order->store_id, 'name' => StoreHelper::getNameShort($order->store)]);

            // Notify picker
            $this->pickerNotify($order->store);
        } catch (BotEventException $e) {
            $this->createEvent($e->getMessage(), OrderEventHelper::TYPE_ASSEMBLY_ERROR, $e->getData());
            $this->transfer();
            throw $e;
        } catch (Exception $e) {
            $this->createEvent($e->getMessage());
            $this->transfer();
            throw $e;
        }
    }

    /**
     * @return void
     * @throws BotEventException
     * @throws StaleObjectException
     * @throws Throwable
     */
    protected function handleYandexEda(): void
    {
        $order = $this->_order;
        $user = $this->_user;

        try {
            $this->checkStatusNew();
            $this->setStatusAccept();

            // Check store
            $this->checkStore();

            // Check stock
            $stockService = new OrderStockService($order);
            $availableStore = $stockService->availableStore($order->store);
            $isAvailableStore = $availableStore['available'];
            $changes = $availableStore['changes'];

            // todo added logic re assembly
            if (!$isAvailableStore) {
                throw new BotEventException(
                    "Не удалось выбрать точку для сборки",
                    $changes ? ['products' => $changes] : []
                );
            }

            // Create assembly
            $assemblyService = new OrderAssemblyService($order, $user);
            $assemblyService->assemblyStore($order->store);

            // Create event
            $eventService = new OrderEventService($this->_order, $this->_user);
            $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_CREATED, ['store_id' => $order->store_id, 'name' => StoreHelper::getNameShort($order->store)]);

            // Notify picker
            $this->pickerNotify($order->store);
        } catch (BotEventException $e) {
            $this->createEvent($e->getMessage(), OrderEventHelper::TYPE_ASSEMBLY_ERROR, $e->getData());
            $this->transfer();
            throw $e;
        } catch (Exception $e) {
            $this->createEvent($e->getMessage());
            $this->transfer();
            throw $e;
        }
    }

    /**
     * @return void
     * @throws BotEventException
     * @throws StaleObjectException
     * @throws Throwable
     */
    protected function handlePickup(): void
    {
        $order = $this->_order;
        $user = $this->_user;

        if ($order->channel == OrderHelper::CHANNEL_KASPI_SHOP) {
            $this->handlePickupKaspi();
            return;
        }

        try {
            $this->checkStatusNew();
            $this->setStatusAccept();

            // Always check has payment
            if (!$this->hasPayment()) {
                return; // waiting payment callback
            }

            // Check store
            $this->checkStore();

            // Check stock
            $stockService = new OrderStockService($order);
            $availableStore = $stockService->availableStore($order->store);
            $isAvailableStore = $availableStore['available'];
            $changes = $availableStore['changes'];

            // todo added logic re assembly
            if (!$isAvailableStore) {
                throw new BotEventException(
                    "Не удалось выбрать точку для сборки",
                    $changes ? ['products' => $changes] : []
                );
            }

            // Create assembly
            $assemblyService = new OrderAssemblyService($order, $user);
            $assemblyService->assemblyStore($order->store);

            // Create event
            $eventService = new OrderEventService($this->_order, $this->_user);
            $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_CREATED, ['store_id' => $order->store_id, 'name' => StoreHelper::getNameShort($order->store)]);

            // Notify picker
            $this->pickerNotify($order->store);

            // Kaspi store assembled
            $this->kaspiStoreAssembled();
        } catch (BotEventException $e) {
            $this->createEvent($e->getMessage(), OrderEventHelper::TYPE_ASSEMBLY_ERROR, $e->getData());
            $this->transfer();
            throw $e;
        } catch (Exception $e) {
            $this->createEvent($e->getMessage());
            $this->transfer();
            throw $e;
        }
    }

    /**
     * @return void
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function handleDeliveryStandard(): void
    {
        $order = $this->_order;
        $user = $this->_user;

        try {
            $this->checkStatusNew();
            $this->setStatusAccept();
            $this->checkCoordinates();

            // Always check has payment
            if (!$this->hasPayment()) {
                return; // waiting payment callback
            }

            $assembledStores = [];
            if ($order->store){
                $assembledStores[] = $order->store;
            } else {
                // Get stores
                $stockService = new OrderStockService($order);
                $stores = $stockService->getStores();
                $this->_logs['stores'] = $stores;

                // Fill distance
                $storesWithDistance = (new StockDistanceService())->fill($order->lat, $order->lng, $stores);
                $this->_logs['storesWithDistance'] = $storesWithDistance;

                // Sort
                $stockSortService = new StockSortService($storesWithDistance, OrderHelper::getProducts($order));
                $store = $stockSortService->getStore();

                if ($store){
                    $this->_logs['store'] = $store;

                    // Set assembled stores
                    $assembledStores[] = $this->getStore($store['id']);
                } else {
                    $stores = $stockSortService->getStores();
                    if (!$stores){
                        throw new DomainException('Не удалось выбрать точку для сборки');
                    }
                    $this->_logs['stores'] = $stores;

                    foreach ($stores as $store){
                        // Set assembled stores
                        $assembledStores[] = $this->getStore($store['id']);
                    }
                }
            }

            // Handle assemblies
            foreach ($assembledStores as $store){
                // Create assembly
                $assemblyService = new OrderAssemblyService($order, $user);
                $assemblyService->assemblyStore($store);

                // Create event
                $eventService = new OrderEventService($this->_order, $this->_user);
                $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_CREATED, ['store_id' => $store->id, 'name' => StoreHelper::getNameShort($store)]);

                // Notify picker
                $this->pickerNotify($store);
            }

            // Kaspi store assembled
            $this->kaspiStoreAssembled();

            // Save log
            $this->saveLogs();
        } catch (Exception $e) {
            // Save log
            $this->saveLogs(false);

            // Event
            $this->createEvent($e->getMessage());

            // Transfer
            $this->transfer();

            throw $e;
        }
    }

    /**
     * @return void
     * @throws StaleObjectException
     * @throws Throwable
     */
    protected function handleHalykExpress(): void
    {
        $order = $this->_order;
        $user = $this->_user;

        try {
            $this->checkStatusNew();
            $this->setStatusAccept();

            // Always check has payment
            if (!$this->hasPayment()) {
                return; // waiting payment callback
            }

            $assembledStores = [];

            // Get stores
            $stockService = new OrderStockService($order);
            $stores = $stockService->getStores();
            $this->_logs['stores'] = $stores;

            // Sort
            $stockSortService = new StockSortService($stores, OrderHelper::getProducts($order));
            $store = $stockSortService->getStore();

            if ($store){
                $this->_logs['store'] = $store;

                // Set assembled stores
                $assembledStores[] = $this->getStore($store['id']);
            } else {
                $stores = $stockSortService->getStores();
                if (!$stores){
                    throw new DomainException('Не удалось выбрать точку для сборки');
                }
                $this->_logs['stores'] = $stores;

                foreach ($stores as $store){
                    // Set assembled stores
                    $assembledStores[] = $this->getStore($store['id']);
                }
            }

            // Handle assemblies
            foreach ($assembledStores as $store){
                // Create assembly
                $assemblyService = new OrderAssemblyService($order, $user);
                $assemblyService->assemblyStore($store);

                // Create event
                $eventService = new OrderEventService($this->_order, $this->_user);
                $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_CREATED, ['store_id' => $store->id, 'name' => StoreHelper::getNameShort($store)]);

                // Notify picker
                $this->pickerNotify($store);
            }

            // Save log
            $this->saveLogs();
        } catch (Exception $e) {
            // Save log
            $this->saveLogs(false);

            // Event
            $this->createEvent($e->getMessage());

            // Transfer
            $this->transfer();

            throw $e;
        }
    }

    /**
     * @return void
     * @throws StaleObjectException
     * @throws Throwable
     */
    protected function handleDeliveryExpress(): void
    {
        $order = $this->_order;
        $user = $this->_user;

        try {
            $this->checkStatusNew();
            $this->setStatusAccept();
            $this->checkCoordinates();

            // Always check has payment
            if (!$this->hasPayment()) {
                return; // waiting payment callback
            }

            $assembledStores = [];
            if ($order->store){
                $assembledStores[] = $order->store;
            } else {
                // Get stores
                $stockService = new OrderStockService($order);
                $stores = $stockService->getStores();
                $this->_logs['stores'] = $stores;

                // Fill distance
                $storesWithDistance = (new StockDistanceService())->fill($order->lat, $order->lng, $stores);
                $this->_logs['storesWithDistance'] = $storesWithDistance;

                // Sort
                $stockSortService = new StockSortService($storesWithDistance, OrderHelper::getProducts($order));
                $store = $stockSortService->getStore();

                // Assembly
                if ($store){
                    $this->_logs['store'] = $store;

                    // Set assembled stores
                    $assembledStores[] = $this->getStore($store['id']);
                } else {
                    $stores = $stockSortService->getStores();
                    if (!$stores){
                        throw new DomainException('Не удалось выбрать точку для сборки');
                    }
                    $this->_logs['stores'] = $stores;

                    foreach ($stores as $store){
                        // Set assembled stores
                        $assembledStores[] = $this->getStore($store['id']);
                    }
                }
            }

            // Handle assemblies
            foreach ($assembledStores as $store){
                // Create assembly
                $assemblyService = new OrderAssemblyService($order, $user);
                $assemblyService->assemblyStore($store);

                // Create event
                $eventService = new OrderEventService($this->_order, $this->_user);
                $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_CREATED, ['store_id' => $store->id, 'name' => StoreHelper::getNameShort($store)]);

                // Notify picker
                $this->pickerNotify($store);
            }

            // Kaspi store assembled
            $this->kaspiStoreAssembled();

            // Save log
            $this->saveLogs();
        } catch (Exception $e) {
            // Save log
            $this->saveLogs(false);

            // Event
            $this->createEvent($e->getMessage());

            // Transfer
            $this->transfer();

            throw $e;
        }
    }

    /**
     * @return void
     * @throws Exception
     * @throws Throwable
     */
    protected function handleDeliveryKaspi(): void
    {
        $order = $this->_order;
        $user = $this->_user;

        try {
            $this->checkStatusNew();
            $this->setStatusAccept();

            // Check store
            $this->checkStore();

            // Check signature
            if ($this->checkSignatureRequired()){
                throw new DomainException('Необходимо подписания кредита');
            }

            // Check stock
            $stockService = new OrderStockService($order);
            $availableStore = $stockService->availableStore($order->store);
            $isAvailableStore = $availableStore['available'];
            $changes = $availableStore['changes'];

            // Is available
            if (!$isAvailableStore) {
                throw new BotEventException(
                    "Не удалось выбрать точку для сборки",
                    $changes ? ['products' => $changes] : []
                );
            }

            // Create assembly
            $assemblyService = new OrderAssemblyService($order, $user);
            $assemblyService->assemblyStore($order->store);

            // Create event
            $eventService = new OrderEventService($this->_order, $this->_user);
            $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_CREATED, ['store_id' => $order->store_id, 'name' => StoreHelper::getNameShort($order->store)]);

            $store = $order->store;
            $orderStore = $this->getOrderStore($order->id, $order->store_id);

            // Store znp old need test
            if ($store->type == StoreHelper::TYPE_STORE){
                (new ZnpService($orderStore))->create();
            } else {
                (new ZnpService($orderStore))->createForShop();
                Yii::$app->queue->push(new PickerAssemblyNotifyJob([
                    'order_id' => $this->_order->id,
                    'store_id' => $store->id,
                ]));
            }

            // Kaspi store assembled
            $this->kaspiStoreAssembled();
        } catch (BotEventException $e) {
            $this->createEvent($e->getMessage(), OrderEventHelper::TYPE_ASSEMBLY_ERROR, $e->getData());
            $this->transfer();
            throw $e;
        } catch (Exception $e) {
            $this->createEvent($e->getMessage());
            $this->transfer();
            throw $e;
        }
    }

    /**
     * @return void
     * @throws Exception
     * @throws Throwable
     */
    protected function handleDeliveryKaspiExpress(): void
    {
        $order = $this->_order;
        $user = $this->_user;

        try {
            $this->checkStatusNew();
            $this->setStatusAccept();

            // Check store
            $this->checkStore();

            // Check signature
            if ($this->checkSignatureRequired()){
                throw new DomainException('Необходимо подписания кредита');
            }

            // Check stock
            $stockService = new OrderStockService($order);
            $availableStore = $stockService->availableStore($order->store);
            $isAvailableStore = $availableStore['available'];
            $changes = $availableStore['changes'];

            // Is available
            if (!$isAvailableStore) {
                throw new BotEventException(
                    "Не удалось выбрать точку для сборки",
                    $changes ? ['products' => $changes] : []
                );
            }

            // Create assembly
            $assemblyService = new OrderAssemblyService($order, $user);
            $assemblyService->assemblyStore($order->store);

            // Create event
            $eventService = new OrderEventService($this->_order, $this->_user);
            $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_CREATED, ['store_id' => $order->store_id, 'name' => StoreHelper::getNameShort($order->store)]);

            $store = $order->store;
            $orderStore = $this->getOrderStore($order->id, $order->store_id);

            // Store znp old need test
            if ($store->type == StoreHelper::TYPE_STORE){
                (new ZnpService($orderStore))->create();
            } else {
                (new ZnpService($orderStore))->createForShop();
                Yii::$app->queue->push(new PickerAssemblyNotifyJob([
                    'order_id' => $this->_order->id,
                    'store_id' => $store->id,
                ]));
            }

            // Kaspi store assembled
            $this->kaspiStoreAssembled();
        } catch (BotEventException $e) {
            $this->createEvent($e->getMessage(), OrderEventHelper::TYPE_ASSEMBLY_ERROR, $e->getData());
            $this->transfer();
            throw $e;
        } catch (Exception $e) {
            $this->createEvent($e->getMessage());
            $this->transfer();
            throw $e;
        }
    }

    /**
     * @return void
     * @throws Exception
     * @throws Throwable
     */
    protected function handleDeliveryJusan(): void
    {
        $order = $this->_order;
        $user = $this->_user;

        try {
            $this->checkStatusNew();
            $this->setStatusAccept();

            // Check store
            $this->checkStore();

            // Check stock
            $stockService = new OrderStockService($order);
            $availableStore = $stockService->availableStore($order->store);
            $isAvailableStore = $availableStore['available'];
            $changes = $availableStore['changes'];

            // Is available
            if (!$isAvailableStore) {
                throw new BotEventException(
                    "Не удалось выбрать точку для сборки",
                    $changes ? ['products' => $changes] : []
                );
            }

            // Create assembly
            $assemblyService = new OrderAssemblyService($order, $user);
            $assemblyService->assemblyStore($order->store);

            // Create event
            $eventService = new OrderEventService($this->_order, $this->_user);
            $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_CREATED, ['store_id' => $order->store_id, 'name' => StoreHelper::getNameShort($order->store)]);

            // Notify picker
            // todo enable after prod
            $this->pickerNotify($order->store);
        } catch (BotEventException $e) {
            $this->createEvent($e->getMessage(), OrderEventHelper::TYPE_ASSEMBLY_ERROR, $e->getData());
            $this->transfer();
            throw $e;
        } catch (Exception $e) {
            $this->createEvent($e->getMessage());
            $this->transfer();
            throw $e;
        }
    }

    /**
     * @return void
     * @throws Exception
     * @throws Throwable
     */
    protected function handleDeliveryWb(): void
    {
        $order = $this->_order;
        $user = $this->_user;

        try {
            $this->checkStatusNew();
            $this->setStatusAccept();

            // Check store
            $this->checkStore();

            // Check stock
            $stockService = new OrderStockService($order);
            $availableStore = $stockService->availableStore($order->store);
            $isAvailableStore = $availableStore['available'];
            $changes = $availableStore['changes'];

            // Is available
            if (!$isAvailableStore) {
                throw new BotEventException(
                    "Не удалось выбрать точку для сборки",
                    $changes ? ['products' => $changes] : []
                );
            }

            // Create assembly
            $assemblyService = new OrderAssemblyService($order, $user);
            $assemblyService->assemblyStore($order->store);

            // Create event
            $eventService = new OrderEventService($this->_order, $this->_user);
            $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_CREATED, ['store_id' => $order->store_id, 'name' => StoreHelper::getNameShort($order->store)]);

            // Transit
            Yii::$app->queue->delay(60)->push(new WbOrderTransitJob([
                'order_id' => $order->id
            ]));
        } catch (BotEventException $e) {
            $this->createEvent($e->getMessage(), OrderEventHelper::TYPE_ASSEMBLY_ERROR, $e->getData());
            $this->transfer();
            throw $e;
        } catch (Exception $e) {
            $this->createEvent($e->getMessage());
            $this->transfer();
            throw $e;
        }
    }

    /**
     * @return void
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function handleDeliveryForte(): void
    {
        $order = $this->_order;
        $user = $this->_user;

        try {
            $this->checkStatusNew();
            $this->setStatusAccept();
            $this->checkCoordinates();

            // Always check has payment
            if (!$this->hasPayment()) {
                return; // waiting payment callback
            }

            $assembledStores = [];
            if ($order->store){
                $assembledStores[] = $order->store;
            } else {
                // Get stores
                $stockService = new OrderStockService($order);
                $stores = $stockService->getStores();
                $this->_logs['stores'] = $stores;

                // Sort
                $stockSortService = new StockSortService($stores, OrderHelper::getProducts($order), StockHelper::SORT_TYPE_STOCK);
                $store = $stockSortService->getStore();

                if (!$store){
                    throw new DomainException('Не удалось выбрать точку для сборки');
                }

                $this->_logs['store'] = $store;

                // Set assembled stores
                $assembledStores[] = $this->getStore($store['id']);
            }

            // Handle assemblies
            foreach ($assembledStores as $store){
                // Create assembly
                $assemblyService = new OrderAssemblyService($order, $user);
                $assemblyService->assemblyStore($store);

                // Create event
                $eventService = new OrderEventService($this->_order, $this->_user);
                $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_CREATED, ['store_id' => $store->id, 'name' => StoreHelper::getNameShort($store)]);

                // Notify picker
                $this->pickerNotify($store);
            }

            // Save log
            $this->saveLogs();
        } catch (Exception $e) {
            // Save log
            $this->saveLogs(false);

            // Event
            $this->createEvent($e->getMessage());

            // Transfer
            $this->transfer();

            throw $e;
        }
    }

    /**
     * @return void
     */
    protected function checkStatusNew(): void
    {
        $order = $this->_order;

        // Check status
        if ($order->status !== OrderHelper::STATUS_NEW){
            throw new DomainException('Статус заказа должен быть Новым');
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function setStatusAccept(): void
    {
        $order = $this->_order;
        $oldStatus = $order->status;

        // Run services
        (new OrderManageService($this->_order))->accept();
        (new OrderHistoryService($this->_order, $this->_user))->create($oldStatus, $order->status);
        (new OrderAssignService($order, $this->_user))->setHandler();
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function transfer(): void
    {
        $order = $this->_order;

        // If the order has already been transferred
        if ($order->executor_id){
            return;
        }

        try {
            // Get operator
            $operator = (new OperatorService())->getOperator();

            // Assign order
            (new OrderAssignService($order, $operator))->assign();
            (new ConsoleService())->run('telegram/message/order-transfer', [$order->id, $operator->id]);

            // Create message
            $this->createEvent(TextHelper::transferOrder($operator->full_name), OrderEventHelper::TYPE_TRANSFER);
        } catch (Exception $e){
            // Create message
            $this->createEvent($e->getMessage(), OrderEventHelper::TYPE_TRANSFER_ERROR);
        }
    }

    /**
     * @param $message
     * @param int $type
     * @param array $data
     * @return void
     * @throws Exception
     */
    protected function createEvent($message, int $type = OrderEventHelper::TYPE_BOT, array $data = []): void
    {
        (new OrderEventService($this->_order, $this->_user))->create($message, $type, $data);
    }

    /**
     * @return bool
     */
    protected function hasPayment(): bool
    {
        $order = $this->_order;
        $types = PaymentHelper::getOnlineTypes();

        // Check payment type
        if (!in_array($order->payment_method, $types)) {
            return true;
        }

        // todo
        // need check order payment

        return true;
    }

    /**
     * @return bool
     */
    protected function isPaid(): bool
    {
        $order = $this->_order;

        $payments = OrderPayment::findAll([
            'order_id' => $order->id,
            'type' => PaymentHelper::TYPE_PAY,
            'status' => PaymentHelper::STATUS_SUCCESS
        ]);

        if (empty($payments)) {
            return false;
        }

        $paidAmount = 0;
        foreach ($payments as $payment) {
            $paidAmount += $payment->provider_cost;
        }

        $orderAmount = OrderHelper::getAmount($order);

        if ($paidAmount < $orderAmount) {
            throw new DomainException("Сумма оплаты ({$paidAmount}) меньше суммы заказа ({$orderAmount})");
        }

        return true;
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
     * @param Store $store
     * @return void
     */
    protected function pickerNotify(Store $store): void
    {
        if ($store->type == StoreHelper::TYPE_STORE){
            (new ConsoleService())->run('stock/znp/create', [$this->_order->id, $store->id]);
        } else {
            Yii::$app->queue->push(new PickerAssemblyNotifyJob([
                'order_id' => $this->_order->id,
                'store_id' => $store->id,
            ]));
        }
    }

    /**
     * @return void
     */
    protected function checkCoordinates(): void
    {
        $order = $this->_order;

        if (!$order->lat || !$order->lng){
            throw new DomainException('Не указаны координаты доставки');
        }
    }

    /**
     * @return void
     */
    protected function checkStore(): void
    {
        $order = $this->_order;

        // Check stock
        if (!$order->store){
            throw new DomainException('В заказе не выбрана точка продаж');
        }
    }

    /**
     * @param $id
     * @return Store|null
     */
    protected function getStore($id): ?Store
    {
        return Store::findOne($id);
    }

    /**
     * @param bool $isSuccess
     * @return void
     * @throws Exception
     */
    protected function saveLogs(bool $isSuccess = true): void
    {
        if (!$this->_logs){
            return;
        }

        if ($isSuccess){
            LogService::success(LogHelper::TARGET_ORDER_BOT, $this->_logs, null, true);
        } else {
            LogService::error(LogHelper::TARGET_ORDER_BOT, $this->_logs, null, true);
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function checkSignatureRequired(): bool
    {
        $order = $this->_order;

        $signatureRequired = false;
        if ($order->channel == OrderHelper::CHANNEL_KASPI_SHOP){
            $signatureRequired = ArrayHelper::getValue($order->extra_fields, 'kaspi_signature_required', false);
        }

        if ($signatureRequired){
            // Create messages
            // (new OrderEventService($order, $this->_user))->create('', OrderEventHelper::TYPE_SIGNATURE_REQUIRED);
        }

        return $signatureRequired;
    }

    /**
     * @param $orderId
     * @param $storeId
     * @return OrderStore
     */
    protected function getOrderStore($orderId, $storeId): OrderStore
    {
        if (!$orderStore = OrderStore::findOne(['order_id' => $orderId, 'store_id' => $storeId])) {
            throw new DomainException("Order store not found");
        }

        return $orderStore;
    }
}