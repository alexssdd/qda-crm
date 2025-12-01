<?php

namespace app\entities;

use yii\db\ActiveQuery;
use app\core\ActiveRecord;

/**
 * This is the model class for table "{{%advert_user}}".
 *
 * @property int $id
 * @property int $advert_id
 * @property int $user_id
 *
 * @property Advert $advert
 * @property User $user
 */
class AdvertUser extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%advert_user}}';
    }

    /**
     * Gets query for [[Advert]].
     *
     * @return ActiveQuery
     */
    public function getAdvert(): ActiveQuery
    {
        return $this->hasOne(Advert::class, ['id' => 'advert_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
