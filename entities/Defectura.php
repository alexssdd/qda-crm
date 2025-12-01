<?php

namespace app\entities;

use yii\db\ActiveQuery;
use app\core\ActiveRecord;

/**
 * This is the model class for table "{{%defectura}}".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $city_id
 * @property int|null $merchant_id
 * @property int|null $product_id
 * @property string|null $product_sku
 * @property string|null $product_name
 * @property float|null $quantity
 * @property float|null $stock
 * @property int|null $created_at
 *
 * @property City $city
 * @property Merchant $merchant
 * @property Product $product
 * @property User $user
 */
class Defectura extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%defectura}}';
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

    /**
     * Gets query for [[Product]].
     *
     * @return ActiveQuery
     */
    public function getProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
