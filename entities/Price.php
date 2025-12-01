<?php

namespace app\entities;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%price}}".
 *
 * @property int $id
 * @property int $product_id
 * @property int|null $city_id
 * @property int|null $type_id
 * @property float|null $price
 * @property string|null $config
 * @property int|null $updated_at
 *
 * @property City $city
 * @property Product $product
 * @property PriceType $type
 */
class Price extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%price}}';
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
     * Gets query for [[Product]].
     *
     * @return ActiveQuery
     */
    public function getProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * Gets query for [[Type]].
     *
     * @return ActiveQuery
     */
    public function getType(): ActiveQuery
    {
        return $this->hasOne(PriceType::class, ['id' => 'type_id']);
    }
}
