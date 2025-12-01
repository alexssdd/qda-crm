<?php

namespace app\entities;

use Yii;
use Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%customer_address}}".
 *
 * @property int $id
 * @property int $customer_id
 * @property int|null $city_id
 * @property string|null $address
 * @property string|null $lat
 * @property string|null $lng
 * @property array|null $config
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Customer $customer
 * @property City $city
 */
class Address extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%address}}';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'city_id' => Yii::t('app', 'City ID'),
            'address' => Yii::t('app', 'Address'),
            'lat' => Yii::t('app', 'Lat'),
            'lng' => Yii::t('app', 'Lng'),
            'config' => Yii::t('app', 'Config'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
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
     * Gets query for [[City]].
     *
     * @return ActiveQuery
     */
    public function getCity(): ActiveQuery
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isDefault(): bool
    {
        return ArrayHelper::getValue($this->config, 'is_default', false);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getTitle()
    {
        return ArrayHelper::getValue($this->config, 'title');
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getHouse()
    {
        return ArrayHelper::getValue($this->config, 'house');
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getApartment()
    {
        return ArrayHelper::getValue($this->config, 'apartment');
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getIntercom()
    {
        return ArrayHelper::getValue($this->config, 'intercom');
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getEntrance()
    {
        return ArrayHelper::getValue($this->config, 'entrance');
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getFloor()
    {
        return ArrayHelper::getValue($this->config, 'floor');
    }

}
