<?php

namespace app\modules\location\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $client_api_url
 * @property string|null $pro_api_url
 * @property string|null $phone_code
 * @property string|null $phone_mask
 * @property array|null $extra_fields
 * @property int $sort
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 */
class Country extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%country}}';
    }
}