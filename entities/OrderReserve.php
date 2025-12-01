<?php

namespace app\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%order_reserve}}".
 *
 * @property int $id
 * @property int $order_id
 * @property int $store_id
 * @property string $number
 * @property string|null $location_from
 * @property string|null $location_to
 * @property array|null $extra_fields
 * @property int $created_at
 * @property int $status
 *
 * @property Order $order
 * @property Store $store
 */
class OrderReserve extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order_reserve}}';
    }

    /**
     * Gets query for [[Order]].
     *
     * @return ActiveQuery
     */
    public function getOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * Gets query for [[Store]].
     *
     * @return ActiveQuery
     */
    public function getStore(): ActiveQuery
    {
        return $this->hasOne(Store::class, ['id' => 'store_id']);
    }
}