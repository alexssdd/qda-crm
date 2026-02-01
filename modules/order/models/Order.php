<?php

namespace app\modules\order\models;

use app\modules\location\models\Country;
use app\modules\location\models\Location;
use Exception;
use yii\db\ActiveQuery;
use app\core\ActiveRecord;
use yii\helpers\ArrayHelper;
use app\core\helpers\OrderHelper;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property int $id
 * @property string $country_code
 * @property int $source_id
 * @property int $channel
 * @property int $type
 * @property string|null $number
 *
 * @property string $phone
 * @property string $name
 * @property numeric $rating
 *
 * @property int|null    $from_location_id
 * @property int|null    $from_address_id
 * @property string      $from_name
 * @property string      $from_address
 * @property float|null  $from_lat
 * @property float|null  $from_lng
 *
 * @property int|null    $to_location_id
 * @property int|null    $to_address_id
 * @property string      $to_name
 * @property string      $to_address
 * @property float|null  $to_lat
 * @property float|null  $to_lng
 *
 * @property int         $category
 * @property int         $price_type
 * @property float|null  $price
 * @property int         $payment_method
 * @property array|null  $assignments
 * @property array|null  $extra_fields
 * @property string|null $comment
 * @property int         $status
 * @property int|null $source_at
 * @property int         $created_at
 *
 * @property Country $country
 * @property OrderHistory[] $histories
 * @property OrderHistory $lastAcceptedHistory
 * @property OrderHistory $lastShippedHistory
 * @property OrderEvent[] $events
 * @property Location $locationFrom
 * @property Location $locationTo
 */
class Order extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%order}}';
    }

    /**
     * @return ActiveQuery
     */
    public function getCountry(): ActiveQuery
    {
        return $this->hasOne(Country::class, ['code' => 'country_code']);
    }

    public function getLocationFrom(): ActiveQuery
    {
        return $this->hasOne(Location::class, ['id' => 'from_location_id']);
    }

    public function getLocationTo(): ActiveQuery
    {
        return $this->hasOne(Location::class, ['id' => 'to_location_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHistories(): ActiveQuery
    {
        return $this->hasMany(OrderHistory::class, ['order_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLastAcceptedHistory(): ActiveQuery
    {
        return $this->hasOne(OrderHistory::class, ['order_id' => 'id'])
            ->andOnCondition(['status_after' => OrderHelper::STATUS_ACCEPTED])
            ->orderBy([OrderHistory::tableName() . '.id' => SORT_DESC]);
    }

    /**
     * @return ActiveQuery
     */
    public function getLastShippedHistory(): ActiveQuery
    {
        return $this->hasOne(OrderHistory::class, ['order_id' => 'id'])
            ->andOnCondition(['status_after' => OrderHelper::STATUS_SHIPPED])
            ->orderBy([OrderHistory::tableName() . '.id' => SORT_DESC]);
    }

    /**
     * @return ActiveQuery
     */
    public function getEvents(): ActiveQuery
    {
        return $this->hasMany(OrderEvent::class, ['order_id' => 'id']);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isPending(): bool
    {
        return ArrayHelper::getValue($this->extra_fields, 'is_pending', false);
    }
}
