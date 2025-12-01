<?php

namespace app\entities;

use Yii;
use yii\db\ActiveQuery;
use app\core\ActiveRecord;

/**
 * This is the model class for table "{{%product}}".
 *
 * @property int $id
 * @property int $merchant_id
 * @property int $brand_id
 * @property string $name
 * @property string|null $image
 * @property string|null $sku
 * @property string|null $barcode
 * @property array|null $config
 * @property int|null $status
 * @property int|null $created_at
 *
 * @property Merchant $merchant
 * @property Brand $brand
 * @property ProductExport[] $exports
 */
class Product extends ActiveRecord
{
    public $stock;
    public $price;
    public $priceAll;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%product}}';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'merchant_id' => Yii::t('app', 'Merchant ID'),
            'name' => Yii::t('app', 'Name'),
            'sku' => Yii::t('app', 'Sku'),
            'barcode' => Yii::t('app', 'Barcode'),
            'image' => Yii::t('app', 'Image'),
            'config' => Yii::t('app', 'Config'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
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
     * Gets query for [[Brand]].
     *
     * @return ActiveQuery
     */
    public function getBrand(): ActiveQuery
    {
        return $this->hasOne(Brand::class, ['id' => 'brand_id']);
    }

    /**
     * Gets query for [[ProductExports]].
     *
     * @return ActiveQuery
     */
    public function getExports(): ActiveQuery
    {
        return $this->hasMany(ProductExport::class, ['product_id' => 'id']);
    }
}
