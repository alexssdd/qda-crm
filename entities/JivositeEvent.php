<?php

namespace app\entities;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%jivosite_event}}".
 *
 * @property int $id
 * @property int $chat_id
 * @property array $data
 * @property int $created_at
 *
 * @property Jivosite $chat
 */
class JivositeEvent extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%jivosite_event}}';
    }

    /**
     * @return ActiveQuery
     */
    public function getChat(): ActiveQuery
    {
        return $this->hasOne(Jivosite::class, ['id' => 'chat_id']);
    }
}
