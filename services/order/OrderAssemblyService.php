<?php

namespace app\services\order;

use Yii;
use Exception;
use Throwable;
use DomainException;
use app\entities\User;
use app\entities\Order;
use app\entities\Store;
use app\entities\OrderStore;
use yii\helpers\ArrayHelper;
use app\entities\OrderProduct;
use yii\db\StaleObjectException;
use app\core\helpers\OrderHelper;
use app\core\helpers\StockHelper;
use app\core\helpers\StoreHelper;
use app\services\StockSortService;
use app\core\helpers\ProductHelper;
use app\entities\OrderStoreProduct;
use app\core\helpers\DeliveryHelper;
use app\core\helpers\OrderEventHelper;
use app\forms\order\OrderAssemblyForm;
use app\core\helpers\OrderStoreHelper;
use app\modules\picker\jobs\PickerRemoveNotifyJob;
use app\modules\stock\services\StockDistanceService;
use app\modules\stock\services\StockAvailableService;

/**
 * Order assembly service
 */
class OrderAssemblyService
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
     * @param OrderProduct $orderProduct
     * @return array
     * @throws Exception
     */
    public function getAssemblyManual(OrderProduct $orderProduct): array
    {
        $order = $this->_order;
        $products = [];
        $assembledProducts = [];

        foreach ($orderProduct->orderStoreProducts as $orderStoreProduct) {
            $assembledProducts[$orderStoreProduct->orderStore->store_id] = $orderStoreProduct->getQuantity();
        }

        return (new StockAvailableService($order->merchant, $order->city_id, $products, $assembledProducts))->getAll();
    }

    /**
     * @param OrderProduct $assemblyProduct
     * @return array
     * @throws Exception
     */
    public function getProductStores(OrderProduct $assemblyProduct): array
    {
        $order = $this->_order;
        $assembledProducts = [];

        foreach ($assemblyProduct->orderStoreProducts as $orderStoreProduct) {
            $assembledProducts[$orderStoreProduct->orderStore->store_id] = $orderStoreProduct->getQuantity();
        }

        $stockService = new OrderStockService($order);
        if ($order->delivery_method == DeliveryHelper::DELIVERY_EMEX){
            $stores = $stockService->getAllStores($assembledProducts);

            // Sort
            $stockSortService = new StockSortService($stores, OrderHelper::getProducts($order), StockHelper::SORT_TYPE_EMEX, $order->city_id);
        } else {
            $stores = $stockService->getStores($assembledProducts);

            // Fill distance
            $storesWithDistance = (new StockDistanceService())->fill($order->lat, $order->lng, $stores);

            // Sort
            $stockSortService = new StockSortService($storesWithDistance, OrderHelper::getProducts($order));
        }

        // Get store groups
        $storeGroups = $stockSortService->sort();

        $result = [];
        foreach ($storeGroups as $sortedStores){
            foreach ($sortedStores as $store){
                $stock = ArrayHelper::getValue($store['stocks'], $assemblyProduct->sku);
                if (!$stock){
                    continue;
                }

                $store['stock'] = $stock;
                $result[] = $store;
            }
        }

        return $result;
    }

    /**
     * @param OrderProduct $orderProduct
     * @param OrderAssemblyForm $form
     * @return void
     * @throws Exception|Throwable
     */
    public function assemblyOrderProduct(OrderProduct $orderProduct, OrderAssemblyForm $form): void
    {
        if (!$orderProduct->orderStoreProducts) {
            $this->insertOrderProduct($orderProduct, $form);
            return;
        }

        $this->updateOrderProduct($orderProduct, $form);
        $this->insertOrderProduct($orderProduct, $form);
        $this->deleteOrderStores();
    }

    /**
     * @param Store $store
     * @return void
     * @throws StaleObjectException
     * @throws Throwable
     * @throws Exception
     */
    public function assemblyStore(Store $store): void
    {
        $order = $this->_order;

        // Check exists
        $orderStore = $this->getOrderStore($store);
        if ($orderStore && $orderStore->status !== OrderStoreHelper::STATUS_CANCELED){
            return;
        }

        // Truncate assemblies
        $this->truncateAssemblies();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($orderStore){
                // Remove current store products
                $this->truncateAssemblyProducts($orderStore);
            } else{
                // Create order store
                $orderStore = new OrderStore();
                $orderStore->order_id = $order->id;
                $orderStore->store_id = $store->id;
                $orderStore->type = OrderStoreHelper::TYPE_SALE;
                $orderStore->created_at = time();
            }

            // Set variables
            $orderStore->status = OrderStoreHelper::STATUS_NEW;

            // Save
            if (!$orderStore->save(false)) {
                throw new DomainException("Order id: $order->id, order store save error");
            }

            foreach ($order->products as $product) {
                $orderStoreProduct = new OrderStoreProduct();
                $orderStoreProduct->order_store_id = $orderStore->id;
                $orderStoreProduct->order_product_id = $product->id;
                $orderStoreProduct->quantity = $product->quantity;

                if (!$orderStoreProduct->save(false)) {
                    throw new DomainException("Order id: $order->id, order store product save error");
                }
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @param OrderProduct $orderProduct
     * @return void
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function removeAssembly(OrderProduct $orderProduct): void
    {
        // todo test picker !!!
        $order = $this->_order;

        foreach ($orderProduct->orderStoreProducts as $orderStoreProduct){
            $orderStoreProduct->delete();
        }

        // Handle order store
        foreach ($order->stores as $orderStore) {
            if ($orderStore->orderStoreProducts){
                continue;
            }

            $store = $orderStore->store;

            // Create event
            $eventService = new OrderEventService($order, $this->_user);
            $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_REMOVED, ['store_id' => $store->id, 'name' => StoreHelper::getNameShort($store)]);

            $orderStore->status = OrderStoreHelper::STATUS_CANCELED;

            if (!$orderStore->save(false)) {
                throw new DomainException("Order store cancelled error");
            }

            // Delete order store
            $orderStore->delete();
        }

        // todo picker notify
    }

    /**
     * @return void
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function removeAssemblyAll(): void
    {
        $this->truncateAssemblies();
    }

    /**
     * @param OrderProduct $orderProduct
     * @param OrderAssemblyForm $form
     * @return void
     * @throws Exception
     */
    protected function insertOrderProduct(OrderProduct $orderProduct, OrderAssemblyForm $form): void
    {
        $order = $this->_order;
        foreach ($form->getData() as $storeId => $quantity) {
            if (!$orderStore = OrderStore::findOne(['order_id' => $order->id, 'store_id' => $storeId])) {
                $orderStore = new OrderStore();
                $orderStore->order_id = $order->id;
                $orderStore->store_id = $storeId;
                $orderStore->type = OrderStoreHelper::TYPE_SALE;
                $orderStore->created_at = time();
                $orderStore->status = OrderStoreHelper::STATUS_NEW;
            }

            if ($orderStore->status == OrderStoreHelper::STATUS_CANCELED) {
                $orderStore->status = OrderStoreHelper::STATUS_NEW;
            }

            if (!$orderStore->save(false)) {
                throw new DomainException("Order id: $order->id, order store save error");
            }

            if (!$orderStoreProduct = OrderStoreProduct::findOne(['order_store_id' => $orderStore->id, 'order_product_id' => $orderProduct->id])) {
                $orderStoreProduct = new OrderStoreProduct();
                $orderStoreProduct->order_store_id = $orderStore->id;
                $orderStoreProduct->order_product_id = $orderProduct->id;
            }
            $orderStoreProduct->quantity = $quantity;

            if (!$orderStoreProduct->save(false)) {
                throw new DomainException("Order id: $order->id, order store product save error");
            }
        }
    }

    /**
     * @param OrderProduct $orderProduct
     * @param OrderAssemblyForm $form
     * @return void
     * @throws Exception
     */
    protected function updateOrderProduct(OrderProduct $orderProduct, OrderAssemblyForm $form): void
    {
        $order = $this->_order;

        foreach ($order->stores as $orderStore) {
            $data = $form->getData();
            if (!array_key_exists($orderStore->store_id, $data)) {
                continue;
            }

            if (!$orderStoreProduct = OrderStoreProduct::findOne([['order_store_id' => $orderStore->id, 'order_product_id' => $orderProduct->id]])) {
                $orderStoreProduct = new OrderStoreProduct();
                $orderStoreProduct->order_store_id = $orderStore->id;
                $orderStoreProduct->order_product_id = $orderProduct->id;
            }

            $orderStoreProduct->quantity = $data[$orderStore->store_id];

            if (!$orderStoreProduct->save(false)) {
                throw new DomainException("Order id: $order->id, order store product save error");
            }
        }
    }

    /**
     * @return void
     * @throws Exception|Throwable
     */
    protected function deleteOrderStores(): void
    {
        $order = $this->_order;

        foreach ($order->stores as $orderStore) {
            $canDelete = true;
            foreach ($orderStore->orderStoreProducts as $orderStoreProduct) {
                if ($orderStoreProduct->quantity > 0) {
                    $canDelete = false;
                    continue;
                }

                $orderStoreProduct->delete();
            }

            if ($canDelete) {
                $orderStore->delete();
            }
        }
    }

    /**
     * @return void
     * @throws Throwable
     * @throws StaleObjectException
     */
    protected function truncateAssemblies(): void
    {
        $order = $this->_order;

        foreach ($order->stores as $orderStore){
            // Check status
            if ($orderStore->status == OrderStoreHelper::STATUS_CANCELED){
                continue;
            }

            $store = $orderStore->store;

            // Create event
            $eventService = new OrderEventService($order, $this->_user);
            $eventService->create('', OrderEventHelper::TYPE_ASSEMBLY_REMOVED, ['store_id' => $store->id, 'name' => StoreHelper::getNameShort($store)]);

            $orderStore->status = OrderStoreHelper::STATUS_CANCELED;
            if (!$orderStore->save(false)) {
                throw new DomainException("Order store cancelled error");
            }

            // Store
            if ($store->type == StoreHelper::TYPE_SHOP){
                Yii::$app->queue->push(new PickerRemoveNotifyJob([
                    'order_id' => $order->id,
                    'store_id' => $order->store_id,
                    'channel' => $order->channel,
                    'data' => $this->getPickerOrder($orderStore)
                ]));

                $orderStore->delete();
            }

            // (new ConsoleService())->runApi('picker/assembly/notify-remove', [$orderStore->id]);
        }
    }

    /**
     * @param OrderStore $orderStore
     * @return void
     * @throws StaleObjectException
     * @throws Throwable
     */
    protected function truncateAssemblyProducts(OrderStore $orderStore): void
    {
        foreach ($orderStore->orderStoreProducts as $orderStoreProduct){
            $orderStoreProduct->delete();
        }
    }

    /**
     * @param Store $store
     * @return OrderStore|null
     */
    protected function getOrderStore(Store $store): ?OrderStore
    {
        return OrderStore::findOne([
            'order_id' => $this->_order->id,
            'store_id' => $store->id
        ]);
    }

    /**
     * @param OrderStore $orderStore
     * @return array
     * @throws Exception
     */
    protected function getPickerOrder(OrderStore $orderStore): array
    {
        $order = $orderStore->order;
        $orderStoreItems = $this->getPickerOrderItems($orderStore);

        return [
            'id' => $orderStore->id,
            'store' => $orderStore->store->number,
            'channel' => OrderHelper::getChannelCode($order),
            'number' => (string)$order->number,
            'source' => [
                'id' => $order->vendor_id,
                'number' => $order->vendor_number,
            ],
            'amount' => $orderStoreItems['amount'],
            'created' => date('Y.m.d h:i', $order->created_at),
            'status' => OrderStoreHelper::getStatusName($orderStore->status),
            'priority' => OrderStoreHelper::getPriority($orderStore),
            'items' => $orderStoreItems['items'],
        ];
    }

    /**
     * @param OrderStore $model
     * @return array
     */
    protected function getPickerOrderItems(OrderStore $model): array
    {
        $items = [];
        $amount = null;

        if (!$model->orderStoreProducts) {
            return [
                'items' => $items,
                'amount' => $amount
            ];
        }

        foreach ($model->orderStoreProducts as $orderStoreProduct) {
            if ($orderStoreProduct->quantity <= 0) {
                continue;
            }

            if ($orderStoreProduct->orderProduct->sku == ProductHelper::PACKAGE_SKU) {
                continue;
            }

            $orderProduct = $orderStoreProduct->orderProduct;
            $amount += $orderStoreProduct->quantity * $orderProduct->price;

            $items[] = [
                'sku' => $orderProduct->sku,
                'name' => $orderProduct->name,
                'barcode' => $orderProduct->barcode,
                'price' => (float) $orderProduct->price,
                'quantity' => (float) $orderStoreProduct->quantity,
                'status' => OrderStoreHelper::getStatusCodeProduct($orderStoreProduct),
                'dimensions' => [
                    'width' => 10.0,
                    'height' => 5.0,
                    'length' => 15.5,
                    'weight' => 0.3
                ],
                'category' => 'Книги / Художественная литература / Всемирная классика',
                'image' => $orderProduct->product ? $orderProduct->product->image : null
            ];
        }

        return [
            'items' => $items,
            'amount' => $amount
        ];
    }
}