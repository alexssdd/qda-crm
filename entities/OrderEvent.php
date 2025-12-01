<?php

namespace app\entities;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%order_event}}".
 *
 * @property int $id
 * @property int $order_id
 * @property int $history_id
 * @property int|null $type
 * @property string|null $message
 * @property array|null $data
 * @property int|null $created_at
 * @property int|null $created_by
 *
 * @property User $createdBy
 * @property OrderHistory $history
 * @property Order $order
 */
class OrderEvent extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%order_event}}';
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
     * Gets query for [[History]].
     *
     * @return ActiveQuery
     */
    public function getHistory(): ActiveQuery
    {
        return $this->hasOne(OrderHistory::class, ['id' => 'history_id']);
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
