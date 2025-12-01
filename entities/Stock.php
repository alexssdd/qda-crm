<?php

namespace app\entities;

use yii\db\ActiveQuery;
use app\core\ActiveRecord;

/**
 * This is the model class for table "{{%stock}}".
 *
 * @property int $id
 * @property int $merchant_id
 * @property int $city_id
 * @property string $sku
 * @property float|null $quantity
 *
 * @property City $city
 * @property Merchant $merchant
 */
class Stock extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%stock}}';
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
     * Gets query for [[Merchant]].
     *
     * @return ActiveQuery
     */
    public function getMerchant(): ActiveQuery
    {
        return $this->hasOne(Merchant::class, ['id' => 'merchant_id']);
    }
}
