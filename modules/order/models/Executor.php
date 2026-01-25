<?php

namespace app\modules\order\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $source_id
 * @property string $country_code
 * @property string $phone
 * @property string $name
 * @property float $rating
 * @property int $is_verified
 * @property int $orders_completed
 * @property int $orders_canceled
 * @property int $source_at
 * @property int $updated_at
 * @property int $status
 * * @property OrderBid[] $bids
 */
class Executor extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%executor}}';
    }

    public function getBids(): ActiveQuery
    {
        return $this->hasMany(OrderBid::class, ['executor_id' => 'id']);
    }
}