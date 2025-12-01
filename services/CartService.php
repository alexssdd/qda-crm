<?php

namespace app\services;

use Yii;
use Exception;
use DomainException;
use app\entities\Store;
use app\entities\Stock;
use app\entities\Order;
use app\entities\Address;
use app\entities\Product;
use app\entities\Customer;
use app\entities\Merchant;
use app\entities\Defectura;
use app\entities\OrderProduct;
use app\core\helpers\UserHelper;
use app\core\helpers\PhoneHelper;
use app\core\helpers\OrderHelper;
use app\forms\cart\CartStoresForm;
use yii\web\NotFoundHttpException;
use app\forms\cart\CartCreateForm;
use app\core\helpers\ProductHelper;
use app\forms\cart\CartCustomerForm;
use app\core\helpers\LeadEventHelper;
use app\forms\cart\CartDefecturaForm;
use app\services\lead\LeadEventService;
use app\forms\cart\CartCalcProductsForm;
use app\forms\cart\CartCalcDeliveryForm;
use app\modules\yandex\services\YandexTaxiService;
use app\modules\stock\services\StockDistanceService;
use app\modules\stock\services\StockAvailableService;

/**
 * Cart service
 */
class CartService
{
    /**
     * @param CartCreateForm $form
     * @return Order
     * @throws Exception
     */
    public function createOrder(CartCreateForm $form): Order
    {
        // Create order
        $order = new Order();
        $order->channel = OrderHelper::CHANNEL_CRM;
        $order->created_by = $form->created_by;
        $order->merchant_id = $form->merchant_id;

        // Customer
        $order->customer_id = $form->customer_id;
        $order->name = $form->name;
        $order->phone = PhoneHelper::getCleanNumber($form->phone);
        $order->comment = $form->comment;

        // Payment
        $order->payment_method = $form->payment_method;

        // Delivery
        $order->delivery_method = $form->delivery_method;
        $order->city_id = $form->city_id;
        $order->store_id = $form->store_id;
        $order->delivery_cost = $form->delivery_cost;
        $order->address = $form->address;
        $order->lat = $form->lat;
        $order->lng = $form->lng;

        // Services fields
        $order->created_at = time();
        $order->status = OrderHelper::STATUS_NEW;
        $order->extra_fields = [
            'phone_ext' => $form->phone_ext,
            'house' => $form->house,
            'apartment' => $form->apartment,
            'intercom' => $form->intercom,
            'entrance' => $form->entrance,
            'floor' => $form->floor,
            'address_type' => $form->address_type,
            'address_title' => $form->address_title,
            'code' => OrderHelper::generateDeliveryCode($order->delivery_method),
            'lead_id' => $form->lead_id
        ];

        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Handle customer
            if (!$order->customer_id){
                $customer = (new CustomerService())->findOrCreate($form->phone, $form->name);
                $order->customer_id = $customer?->id;
            }

            // Save order
            if (!$order->save(false)) {
                throw new DomainException('Order create error');
            }

            $cost = 0;
            foreach ($form->products as $id => $item) {
                $product = Product::findOne($id);
                if (!$product){
                    throw new DomainException('The product was not found: ' . $id);
                }

                // Check product price
                $productPrice = (new ProductService($product))->getPrice($form->customer_id);
                if (!$productPrice){
                    throw new DomainException('Empty price for product: ' . $product->name);
                }

                $orderItem = new OrderProduct();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $product->id;
                $orderItem->price_type_id = $productPrice['price_type_id'];
                $orderItem->sku = $product->sku;
                $orderItem->name = $product->name;
                $orderItem->price = $productPrice['price'];
                $orderItem->quantity = (float)$item['quantity'];
                $orderItem->quantity_original = (float)$item['quantity'];
                $orderItem->m3 = ProductHelper::getM3($product);
                $orderItem->weight = ProductHelper::getWeight($product);
                $orderItem->extra_fields = [
                    'sizes' => ProductHelper::getSizes($product),
                    'block' => ProductHelper::getBlock($product),
                    'type' => ProductHelper::getType($product)
                ];

                $cost += $orderItem->price * $orderItem->quantity;

                if (!$orderItem->save(false)) {
                    throw new DomainException('Order item create error');
                }
            }

            // Save order for cost
            $order->amount = $cost;
            $extraFields = $order->extra_fields;
            $extraFields['amount_original'] = $cost;
            $order->extra_fields = $extraFields;
            $order->save(false);

            // Lead event
            if ($form->lead_id){
                $lead = OrderHelper::getLead($order);
                (new LeadEventService($lead, $order->createdBy))->create('', LeadEventHelper::TYPE_ORDER_CREATED);
            }

            $transaction->commit();

            // Generate number
            $order->generateNumber();

            return $order;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @param CartCustomerForm $form
     * @return array|null
     */
    public function customer(CartCustomerForm $form): ?array
    {
        $customer = Customer::findOne([
            'phone' => PhoneHelper::getCleanNumber($form->phone)
        ]);

        if (!$customer){
            return null;
        }

        $addresses = $customer->getAddresses()
            ->andFilterWhere(['city_id' => $form->city_id])
            ->all();

        return [
            'id' => $customer->id,
            'name' => $customer->name,
            'addresses' => array_map(function (Address $address){
                return [
                    'id' => $address->id,
                    'address' => $address->address,
                    'lat' => $address->lat,
                    'lng' => $address->lng,
                ];
            }, $addresses),
        ];
    }

    /**
     * @param CartCalcProductsForm $form
     * @return array
     * @throws NotFoundHttpException
     */
    public function calcProducts(CartCalcProductsForm $form): array
    {
        $result = [
            'products' => [],
            'cost' => 0
        ];

        foreach ($form->products as $item) {
            $product = $this->findProduct($item['id']);
            $productPrice = (new ProductService($product))->getPrice($form->customer_id);
            
            // Check product price
            if (!$productPrice){
                throw new DomainException('Empty price for product: ' . $product->name);
            }

            $price = $productPrice['price'];
            $cost = (float)$item['quantity'] * $price;

            $result['products'][] = [
                'id' => (int)$item['id'],
                'price' => $price,
                'cost' => $cost
            ];

            $result['cost'] += $cost;
        }

        return $result;
    }

    /**
     * @param CartCalcDeliveryForm $form
     * @return array
     * @throws Exception
     */
    public function calcDelivery(CartCalcDeliveryForm $form): array
    {
        $stores = (new StockAvailableService($form->getMerchant(), $form->city_id, $form->products))->getStores();

        // Fill distance
        $storesWithDistance = (new StockDistanceService())->fill($form->lat, $form->lng, $stores);

        // Sort
        $stockSortService = new StockSortService($storesWithDistance, $form->products);
        $storeGroups = $stockSortService->sort();

        // Check store groups
        if (!$storeGroups['enough']) {
            throw new DomainException("Не удалось выбрать точку для сборки");
        }

        // Get store
        $store = Store::findOne($storeGroups['enough'][0]['id']);

        // Get cost
        $cost = (new YandexTaxiService())->getPrice($store, $form->lat, $form->lng);

        return [
            'cost' => $cost,
            'store_id' => $store->id
        ];
    }

    /**
     * @param CartDefecturaForm $form
     * @return void
     * @throws Exception
     */
    public function defectura(CartDefecturaForm $form): void
    {
        $product = Product::findOne($form->product_id);
        $stock = Stock::findOne([
            'merchant_id' => $product->merchant_id,
            'city_id' => $form->city_id,
            'sku' => $product->sku
        ]);

        $defectura = new Defectura();
        $defectura->user_id = UserHelper::getIdentity()->id;
        $defectura->city_id = $form->city_id;
        $defectura->merchant_id = $product?->merchant_id;
        $defectura->product_id = $product?->id;
        $defectura->product_sku = $product?->sku;
        $defectura->product_name = $product ? $product->name : '';
        $defectura->quantity = $form->quantity;
        $defectura->stock = $stock ? $stock->quantity : 0;
        $defectura->created_at = time();
        if (!$defectura->save()){
            throw new DomainException($defectura->getErrorSummary(true)[0]);
        }
    }

    /**
     * @param CartStoresForm $form
     * @return array
     * @throws Exception
     */
    public function stores(CartStoresForm $form): array
    {
        if (!$merchant = Merchant::findOne($form->merchant_id)) {
            throw new DomainException("Merchant id: $form->merchant_id not found");
        }

        // Get stores
        $storeGroups = (new StockAvailableService($merchant, $form->city_id, $form->products))->getStores();

        $result = [];
        foreach ($storeGroups as $stores){
            $result = array_merge($result, $stores);
        }

        return $result;
    }

    /**
     * @param $id
     * @return Product
     * @throws NotFoundHttpException
     */
    protected function findProduct($id): Product
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}