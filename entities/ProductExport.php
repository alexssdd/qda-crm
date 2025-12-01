<?php

namespace app\entities;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%product_export}}".
 *
 * @property int $id
 * @property int $product_id
 * @property string $sku
 * @property int $channel
 * @property int|null $code
 * @property int|null $status
 *
 * @property Product $product
 */
class ProductExport extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%product_export}}';
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
}
