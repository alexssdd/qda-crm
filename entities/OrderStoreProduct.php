<?php

namespace app\entities;

use yii\db\ActiveQuery;
use app\core\ActiveRecord;

/**
 * This is the model class for table "{{%order_store_product}}".
 *
 * @property int $order_store_id
 * @property int $order_product_id
 * @property float|null $quantity
 * @property float|null $quantity_available
 *
 * @property OrderProduct $orderProduct
 * @property OrderStore $orderStore
 */
class OrderStoreProduct extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%order_store_product}}';
    }

    /**
     * @return ActiveQuery
     */
    public function getOrderProduct(): ActiveQuery
    {
        return $this->hasOne(OrderProduct::class, ['id' => 'order_product_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrderStore(): ActiveQuery
    {
        return $this->hasOne(OrderStore::class, ['id' => 'order_store_id']);
    }

    /**
     * @return float
     */
    public function getQuantity(): float
    {
        return (float)$this->quantity;
    }

    /**
     * @return bool
     */
    public function hasQuantity(): bool
    {
        return $this->quantity > 0;
    }

    /**
     * @return string
     */
    public function getQuantityLabel(): string
    {
        return (float)$this->quantity . ' шт';
    }
}
