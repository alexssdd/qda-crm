<?php

namespace app\entities;

use Yii;
use Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%city}}".
 *
 * @property int $id
 * @property int $country_id
 * @property string $name
 * @property string $name_kk
 * @property array|null $config
 * @property int|null $status
 *
 * @property Country $country
 * @property Store[] $stores
 */
class City extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%city}}';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'country_id' => Yii::t('app', 'Country ID'),
            'name' => Yii::t('app', 'Name'),
            'name_kk' => Yii::t('app', 'Name Kk'),
            'config' => Yii::t('app', 'Config'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Country]].
     *
     * @return ActiveQuery
     */
    public function getCountry(): ActiveQuery
    {
        return $this->hasOne(Country::class, ['id' => 'country_id']);
    }

    /**
     * Gets query for [[Stores]].
     *
     * @return ActiveQuery
     */
    public function getStores(): ActiveQuery
    {
        return $this->hasMany(Store::class, ['city_id' => 'id']);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getLat()
    {
        return ArrayHelper::getValue($this->config, 'lat');
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getLng()
    {
        return ArrayHelper::getValue($this->config, 'lng');
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getDeliveryId()
    {
        return ArrayHelper::getValue($this->config, 'delivery_id');
    }
}
