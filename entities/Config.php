<?php

namespace app\entities;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%config}}".
 *
 * @property int $id
 * @property string $key
 * @property int|null $type
 * @property array $values
 * @property array $encrypted_values
 */
class Config extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%config}}';
    }
}
