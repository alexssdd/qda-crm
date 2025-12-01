<?php

namespace app\entities;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%store_stock}}".
 *
 * @property int $id
 * @property int $store_id
 * @property string $sku
 * @property float|null $quantity
 *
 * @property Store $store
 */
class StoreStock extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%store_stock}}';
    }

    /**
     * Gets query for [[Store]].
     *
     * @return ActiveQuery
     */
    public function getStore(): ActiveQuery
    {
        return $this->hasOne(Store::class, ['id' => 'store_id']);
    }
}
