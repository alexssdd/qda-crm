<?php

namespace app\entities;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%log}}".
 *
 * @property int $id
 * @property int $target
 * @property string $data
 * @property numeric $runtime
 * @property int $created_at
 * @property int $status
 */
class Log extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%log}}';
    }
}
