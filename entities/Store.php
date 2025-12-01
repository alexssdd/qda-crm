<?php

namespace app\entities;

use Yii;
use yii\db\ActiveQuery;
use app\core\ActiveRecord;

/**
 * This is the model class for table "{{%store}}".
 *
 * @property int $id
 * @property int $merchant_id
 * @property int $city_id
 * @property int|null $type
 * @property string $name
 * @property string|null $number
 * @property string|null $address
 * @property string|null $lat
 * @property string|null $lng
 * @property array|null $config
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property Merchant $merchant
 * @property City $city
 */
class Store extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%store}}';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'merchant_id' => Yii::t('app', 'Merchant ID'),
            'city_id' => Yii::t('app', 'City ID'),
            'type' => Yii::t('app', 'Type'),
            'name' => Yii::t('app', 'Name'),
            'name_short' => Yii::t('app', 'Name Short'),
            'number' => Yii::t('app', 'Number'),
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
     * Gets query for [[Merchant]].
     *
     * @return ActiveQuery
     */
    public function getMerchant(): ActiveQuery
    {
        return $this->hasOne(Merchant::class, ['id' => 'merchant_id']);
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
}
