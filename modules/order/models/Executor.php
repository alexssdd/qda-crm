<?php

namespace app\modules\order\models;

use app\modules\location\models\Country;
use app\modules\location\models\Location;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $source_id
 * @property string $country_code
 * @property int|null $location_id
 * @property string $phone
 * @property string $name
 * @property float $rating
 * @property int $is_verified
 * @property int $orders_completed
 * @property int $orders_canceled
 * @property int $registered_at
 * @property int $updated_at
 * @property int $status
 *
 * @property Country $country
 * @property Location|null $location
 * @property ExecutorService[] $services
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

    public function getLocation(): ActiveQuery
    {
        return $this->hasOne(Location::class, ['id' => 'location_id']);
    }

    public function getServices(): ActiveQuery
    {
        return $this->hasMany(ExecutorService::class, ['executor_id' => 'id']);
    }

    public function getBids(): ActiveQuery
    {
        return $this->hasMany(OrderBid::class, ['executor_id' => 'id']);
    }
}
