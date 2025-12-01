<?php

namespace app\entities;

use Yii;
use yii\db\ActiveQuery;
use app\core\ActiveRecord;

/**
 * This is the model class for table "{{%merchant}}".
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property array|null $config
 * @property int|null $status
 *
 * @property Product[] $products
 * @property Store[] $stores
 */
class Merchant extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%merchant}}';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'config' => Yii::t('app', 'Config'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[Products]].
     *
     * @return ActiveQuery
     */
    public function getProducts(): ActiveQuery
    {
        return $this->hasMany(Product::class, ['merchant_id' => 'id']);
    }

    /**
     * Gets query for [[Stores]].
     *
     * @return ActiveQuery
     */
    public function getStores(): ActiveQuery
    {
        return $this->hasMany(Store::class, ['merchant_id' => 'id']);
    }
}
