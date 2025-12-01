<?php

namespace app\entities;

use Yii;
use Exception;
use yii\db\ActiveQuery;
use app\core\ActiveRecord;

/**
 * This is the model class for table "{{%lead}}".
 *
 * @property int $id
 * @property int|null $handler_id
 * @property int|null $city_id
 * @property int|null $customer_id
 * @property int|null $brand_id
 * @property int|null $executor_id
 * @property int|null $created_by
 * @property int|null $channel
 * @property int|null $number
 * @property int|null $vendor_id
 * @property int|null $vendor_number
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $title
 * @property string|null $extra_fields
 * @property int $created_at
 * @property int $completed_at
 * @property int $status
 *
 * @property Brand $brand
 * @property City $city
 * @property User $createdBy
 * @property Customer $customer
 * @property User $executor
 * @property User $handler
 * @property CareHistory[] $histories
 * @property CareEvent[] $events
 */
class Lead extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%lead}}';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'handler_id' => Yii::t('app', 'Handler ID'),
            'city_id' => Yii::t('app', 'City ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'brand_id' => Yii::t('app', 'Brand ID'),
            'executor_id' => Yii::t('app', 'Executor ID'),
            'created_by' => Yii::t('app', 'Created By'),
            'channel' => Yii::t('app', 'Channel'),
            'number' => Yii::t('app', 'Number'),
            'vendor_id' => Yii::t('app', 'Vendor ID'),
            'vendor_number' => Yii::t('app', 'Vendor Number'),
            'name' => Yii::t('app', 'Name'),
            'phone' => Yii::t('app', 'Phone'),
            'title' => Yii::t('app', 'Lead Title'),
            'extra_fields' => Yii::t('app', 'Extra Fields'),
            'created_at' => Yii::t('app', 'Created At'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[Brand]].
     *
     * @return ActiveQuery
     */
    public function getBrand(): ActiveQuery
    {
        return $this->hasOne(Brand::class, ['id' => 'brand_id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return ActiveQuery
     */
    public function getCity(): ActiveQuery
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
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
     * Gets query for [[Customer]].
     *
     * @return ActiveQuery
     */
    public function getCustomer(): ActiveQuery
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }

    /**
     * Gets query for [[Executor]].
     *
     * @return ActiveQuery
     */
    public function getExecutor(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'executor_id']);
    }

    /**
     * Gets query for [[Handler]].
     *
     * @return ActiveQuery
     */
    public function getHandler(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'handler_id']);
    }

    /**
     * Gets query for [[histories]].
     *
     * @return ActiveQuery
     */
    public function getHistories(): ActiveQuery
    {
        return $this->hasMany(LeadHistory::class, ['lead_id' => 'id']);
    }

    /**
     * Gets query for [[Events]].
     *
     * @return ActiveQuery
     */
    public function getEvents(): ActiveQuery
    {
        return $this->hasMany(LeadEvent::class, ['lead_id' => 'id']);
    }

    /**
     * @param $insert
     * @param $changedAttributes
     * @return void
     * @throws Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!$this->number) {
            $this->generateNumber();
            $this->updateAttributes(['number']);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function generateNumber()
    {
        $this->number = ($this->id * 88) + random_int(100, 999);
    }
}
