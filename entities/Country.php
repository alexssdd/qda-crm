<?php

namespace app\entities;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%country}}".
 *
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property array|null $config
 * @property int|null $status
 *
 * @property City[] $cities
 */
class Country extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%country}}';
    }
}
