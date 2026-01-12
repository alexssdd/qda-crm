<?php

namespace app\modules\location\models;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $country_id
 * @property int|null $parent_id
 * @property int $type
 * @property string $name
 * @property string $search_keywords
 * @property string $extra_fields
 * @property resource $polygon
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Country $country
 */
class Location extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%location}}';
    }

    public function getCountry(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Country::class, ['id' => 'country_id']);
    }
}