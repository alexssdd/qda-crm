<?php

namespace app\entities;


use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%order_receipt}}".
 *
 * @property int $id
 * @property int $order_id
 * @property int $type
 * @property float|null $amount
 * @property mixed|null $products
 * @property int $created_at
 *
 * @property Order $order
 */
class OrderReceipt extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order_receipt}}';
    }

    /**
     * @return ActiveQuery
     */
    public function getOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }
}