<?php

namespace app\entities;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%order_product}}".
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $product_id
 * @property int|null $price_type_id
 * @property string|null $vendor_id
 * @property string|null $sku
 * @property string|null $barcode
 * @property string|null $name
 * @property string|null $manufacturer
 * @property float|null $price
 * @property float|null $quantity
 * @property float|null $quantity_original
 * @property float|null $m3
 * @property float|null $weight
 * @property array|null $extra_fields
 *
 * @property Order $order
 * @property Merchant $merchant
 * @property Product $product
 * @property PriceType $priceType
 * @property OrderStoreProduct[] $orderStoreProducts
 */
class OrderProduct extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%order_product}}';
    }

    /**
     * @return ActiveQuery
     */
    public function getOrder(): ActiveQuery
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMerchant(): ActiveQuery
    {
        return $this->hasOne(Merchant::class, ['id' => 'merchant_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPriceType(): ActiveQuery
    {
        return $this->hasOne(PriceType::class, ['id' => 'price_type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrderStoreProducts(): ActiveQuery
    {
        return $this->hasMany(OrderStoreProduct::class, ['order_product_id' => 'id']);
    }
}
