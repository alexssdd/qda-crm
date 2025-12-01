<?php

namespace app\services\order;

use Yii;
use Exception;
use DomainException;
use app\entities\Order;
use yii\helpers\ArrayHelper;
use app\entities\OrderProduct;
use app\forms\order\OrderProductAddForm;
use app\forms\order\OrderProductUpdateForm;

/**
 * Order product service
 */
class OrderProductService
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
     * @param OrderProductAddForm $form
     * @return void
     * @throws Exception
     */
    public function add(OrderProductAddForm $form): void
    {
        $order = $this->_order;
        $product = $form->getProduct();
        $orderProduct = OrderProduct::find()
            ->andWhere(['order_id' => $order->id, 'product_id' => $form->product_id])
            ->one();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $amount = 0;
            if (!$orderProduct) {
                $orderProduct = new OrderProduct();
                $orderProduct->order_id = $order->id;
                $orderProduct->product_id = $form->product_id;
                $orderProduct->sku = $product->sku;
                $orderProduct->barcode = $product->barcode;
                $orderProduct->name = $product->name;
                $orderProduct->quantity = $form->quantity;
                $orderProduct->quantity_original = $form->quantity;
            } else {
                $orderProduct->quantity = ($orderProduct->quantity + $form->quantity);
            }

            // Set price
            $orderProduct->price = $form->getPrice();

            // Save
            if (!$orderProduct->save(false)) {
                throw new Exception('Order add product not save');
            }

            /** @var OrderProduct[] $orderProducts */
            $orderProducts = $order->getProducts()->all();
            foreach ($orderProducts as $orderProduct) {
                $amount += ($orderProduct->price * $orderProduct->quantity);
            }

            $order->amount = $amount;
            if (!$order->save(false)) {
                throw new Exception('After add order product not save');
            }

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @param OrderProductUpdateForm $form
     * @return void
     */
    public function update(OrderProductUpdateForm $form): void
    {
        $order = $this->_order;
        $transaction = Yii::$app->db->beginTransaction();
        $amount = 0;

        try {
            foreach ($order->products as $orderProduct) {
                $quantity = ArrayHelper::getValue($form->products, $orderProduct->id, 0);
                $orderProduct->quantity = $quantity;
                $orderProduct->save(false);

                $amount += ($orderProduct->quantity * $orderProduct->price);
            }

            $order->amount = $amount;
            $order->save(false);

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw new DomainException($e->getMessage());
        }
    }
}