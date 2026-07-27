<?php

namespace app\modules\order\models;

use app\modules\location\models\Country;
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
 *
 * @property Country $country
 * @property OrderBid[] $bids
 */
class Executor extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%executor}}';
    }

    public function getCountry(): ActiveQuery
    {
        return $this->hasOne(Country::class, ['code' => 'country_code']);
    }

    public function getBids(): ActiveQuery
    {
        return $this->hasMany(OrderBid::class, ['executor_id' => 'id']);
    }
}
