<?php

namespace app\entities;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property int $id
 * @property int|null $parent_id
 * @property int|null $vendor_id
 * @property string $name
 * @property array $config
 */
class Category extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%category}}';
    }
}
