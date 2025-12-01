<?php

namespace app\entities;

use Yii;
use Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%care}}".
 *
 * @property int $id
 * @property int|null $handler_id
 * @property int|null $city_id
 * @property int|null $customer_id
 * @property int|null $executor_id
 * @property int|null $created_by
 * @property int|null $type
 * @property int|null $channel
 * @property int|null $language
 * @property int|null $number
 * @property int|null $order_number
 * @property string|null $vendor_id
 * @property string|null $vendor_number
 * @property float|null $rating
 * @property string|null $name
 * @property string|null $phone
 * @property string|null $text
 * @property int|null $count_request
 * @property int|null $count_problem
 * @property int|null $delivery_late
 * @property int|null $complaint_object
 * @property string|null $complaint_reason
 * @property string|null $complaint_validity
 * @property string|null $complaint_personal
 * @property string|null $category
 * @property string|null $solution_measures
 * @property string|null $solution_text
 * @property string|null $compensation
 * @property string|null $store_number
 * @property int|null $final_status
 * @property int|null $callback_status
 * @property int|null $callback_rating
 * @property array|null $extra_fields
 * @property int $created_at
 * @property int|null $completed_at
 * @property int $status
 *
 * @property City $city
 * @property User $createdBy
 * @property Customer $customer
 * @property User $executor
 * @property User $handler
 * @property CareEvent[] $events
 * @property CareHistory[] $histories
 */
class Care extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%care}}';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('care', 'ID'),
            'handler_id' => Yii::t('care', 'Handler ID'),
            'city_id' => Yii::t('care', 'City ID'),
            'customer_id' => Yii::t('care', 'Customer ID'),
            'executor_id' => Yii::t('care', 'Executor ID'),
            'created_by' => Yii::t('care', 'Created By'),
            'type' => Yii::t('care', 'Type'),
            'channel' => Yii::t('care', 'Channel'),
            'language' => Yii::t('care', 'Language'),
            'number' => Yii::t('care', 'Number'),
            'order_number' => Yii::t('care', 'Order Number'),
            'rating' => Yii::t('care', 'Rating'),
            'name' => Yii::t('care', 'Name'),
            'phone' => Yii::t('care', 'Phone'),
            'text' => Yii::t('care', 'Care Text'),
            'count_request' => Yii::t('care', 'Count Request'),
            'count_problem' => Yii::t('care', 'Count Problem'),
            'delivery_late' => Yii::t('care', 'Delivery Late'),
            'complaint_object' => Yii::t('care', 'Complaint Object'),
            'complaint_reason' => Yii::t('care', 'Complaint Reason'),
            'complaint_validity' => Yii::t('care', 'Complaint Validity'),
            'complaint_personal' => Yii::t('care', 'Complaint Personal'),
            'category' => Yii::t('care', 'Category'),
            'solution_measures' => Yii::t('care', 'Solution Measures'),
            'solution_text' => Yii::t('care', 'Solution Text'),
            'compensation' => Yii::t('care', 'Compensation'),
            'store_number' => Yii::t('care', 'Store Number'),
            'final_status' => Yii::t('care', 'Final Status'),
            'extra_fields' => Yii::t('care', 'Extra Fields'),
            'created_at' => Yii::t('care', 'Created At'),
            'completed_at' => Yii::t('care', 'Completed At'),
            'status' => Yii::t('care', 'Status'),
        ];
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
        return $this->hasMany(CareHistory::class, ['care_id' => 'id']);
    }

    /**
     * Gets query for [[Events]].
     *
     * @return ActiveQuery
     */
    public function getEvents(): ActiveQuery
    {
        return $this->hasMany(CareEvent::class, ['care_id' => 'id']);
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
