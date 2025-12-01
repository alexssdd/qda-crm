<?php

namespace app\entities;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%product_content}}".
 *
 * @property int $id
 * @property int $product_id
 * @property int|null $category_id
 * @property string $description
 * @property array $config
 */
class ProductContent extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%product_content}}';
    }
}
