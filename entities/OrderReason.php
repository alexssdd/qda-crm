<?php

namespace app\entities;

use yii\db\ActiveQuery;
use app\core\ActiveRecord;

/**
 * This is the model class for table "{{%order_reason}}".
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $user_id
 * @property int|null $action
 * @property string|null $reason
 * @property string|null $reason_additional
 * @property string|null $text
 * @property array|null $extra_fields
 * @property int|null $created_at
 *
 * @property Order $order
 * @property User $user
 */
class OrderReason extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%order_reason}}';
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
    public function getReason(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['id' => 'reason_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['id' => 'user_id']);
    }
}
