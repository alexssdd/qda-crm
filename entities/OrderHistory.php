<?php

namespace app\entities;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%order_history}}".
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $type
 * @property string|null $message
 * @property int|null $status_before
 * @property int|null $status_after
 * @property int|null $created_at
 * @property int|null $created_by
 *
 * @property OrderEvent[] $orderEvents
 * @property User $createdBy
 * @property Order $order
 */
class OrderHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%order_history}}';
    }

    /**
     * Gets query for [[OrderEvents]].
     *
     * @return ActiveQuery
     */
    public function getOrderEvents(): ActiveQuery
    {
        return $this->hasMany(OrderEvent::class, ['history_id' => 'id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return ActiveQuery
     */
    public function getCreatedBy(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
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
}
