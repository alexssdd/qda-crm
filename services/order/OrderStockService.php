<?php

namespace app\services\order;

use Exception;
use DomainException;
use app\entities\Order;
use app\entities\Store;
use app\core\helpers\StoreHelper;
use app\core\helpers\OrderHelper;
use app\core\helpers\DeliveryHelper;
use app\modules\stock\services\StockAvailableService;

/**
 * Order stock service
 */
class OrderStockService
{
    private $_order;

    /**
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->_order = $order;
    }

    /**
     * @param array $assembledProducts
     * @return array
     * @throws Exception
     */
    public function getStores(array $assembledProducts = []): array
    {
        $order = $this->_order;
        $products = OrderHelper::getProducts($order);
        if (!$products){
            throw new DomainException("Order id: $order->id, empty order products");
        }

        // Get stocks
        $storeGroups = (new StockAvailableService($order->merchant, $order->city_id, $products, $assembledProducts))->getStores();

        return $this->filterGroups($storeGroups);
    }

    /**
     * @param array $assembledProducts
     * @return array
     * @throws Exception
     */
    public function getAllStores(array $assembledProducts = []): array
    {
        $order = $this->_order;
        $products = OrderHelper::getProducts($order);
        if (!$products){
            throw new DomainException("Order id: $order->id, empty order products");
        }

        return (new StockAvailableService($order->merchant, null, $products, $assembledProducts))->getStores();
    }

    /**
     * @param Store $store
     * @return array
     * @throws Exception
     */
    public function availableStore(Store $store): array
    {
        $order = $this->_order;
        $products = OrderHelper::getProducts($order);

        if (!$products){
            throw new DomainException("Order id: $order->id, empty order products");
        }

        return (new StockAvailableService($order->merchant, $order->city_id, $products))->check($store);
    }

    /**
     * @param $storeGroups
     * @return array
     */
    protected function filterGroups($storeGroups): array
    {
        $order = $this->_order;

        if ($order->delivery_method == DeliveryHelper::DELIVERY_EXPRESS){
            if (OrderHelper::isChannelSite($order) || OrderHelper::isChannelMobile($order)){
                $this->removeStores($storeGroups);
            }
        }

        return $storeGroups;
    }

    /**
     * @param $storeGroups
     * @return void
     */
    protected function removeStores(&$storeGroups): void
    {
        foreach ($storeGroups as &$group){
            foreach ($group as $i => $store){
                if ($store['type'] == StoreHelper::TYPE_STORE){
                    unset($group[$i]);
                }
            }
        }
    }
}