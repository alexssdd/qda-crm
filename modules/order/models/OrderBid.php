<?php

namespace app\modules\order\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $source_id
 * @property string $country_code
 * @property int $order_id
 * @property int $executor_id
 * @property float $price
 * @property string|null $comment
 * @property int $is_executor
 * @property int $source_at
 * @property int $status
 * * @property Order $order
 * @property Executor $executor
 */
class OrderBid extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%order_bid}}';
    }

    public function getOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    public function getExecutor(): ActiveQuery
    {
        return $this->hasOne(Executor::class, ['id' => 'executor_id']);
    }
}