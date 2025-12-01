<?php

namespace app\entities;

use Yii;
use Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%token}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $token
 * @property integer $expired_at
 *
 * @property User $user
 */
class Token extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%token}}';
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @param $expire
     * @throws Exception
     */
    public function generateToken($expire)
    {
        $this->expired_at = $expire;
        $this->token = Yii::$app->security->generateRandomString();
    }
}