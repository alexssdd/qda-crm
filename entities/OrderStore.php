<?php

namespace app\entities;

use yii\db\ActiveQuery;
use app\core\ActiveRecord;

/**
 * This is the model class for table "{{%order_store}}".
 *
 * @property int $id
 * @property int $order_id
 * @property int $store_id
 * @property int|null $type
 * @property array|null $extra_fields
 * @property int $created_at
 * @property int $status
 *
 * @property Order $order
 * @property Store $store
 * @property OrderStoreProduct[] $orderStoreProducts
 */
class OrderStore extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%order_store}}';
    }

    /**
     * @return ActiveQuery
     */
    public function getOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getStore(): ActiveQuery
    {
        return $this->hasOne(Store::class, ['id' => 'store_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrderStoreProducts(): ActiveQuery
    {
        return $this->hasMany(OrderStoreProduct::class, ['order_store_id' => 'id']);
    }
}
