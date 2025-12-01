<?php

namespace app\entities;

use Yii;
use Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use app\core\helpers\UserHelper;

/**
 * User model
 *
 * @property integer $id
 * @property string $role
 * @property string $phone
 * @property string|null $email
 * @property string|null $full_name
 * @property string $avatar
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property array|null $config
 * @property integer $status
 * @property integer|null $state
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    public $orders_count;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('user', 'ID'),
            'role' => Yii::t('user', 'Role'),
            'full_name' => Yii::t('user', 'Full Name'),
            'phone' => Yii::t('user', 'Phone'),
            'status' => Yii::t('user', 'Status'),
            'state' => Yii::t('user', 'State'),
            'created_at' => Yii::t('user', 'Created At'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => UserHelper::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->joinWith('tokens t')
            ->andWhere(['t.token' => $token])
            ->andWhere(['>', 't.expired_at', time()])
            ->one();
    }

    /**
     * Finds user by phone
     *
     * @param string $phone
     * @return static|null
     */
    public static function findByPhone(string $phone): ?User
    {
        return static::findOne(['phone' => $phone, 'status' => UserHelper::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @param $password
     * @return void
     * @throws Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Remove verification token
     */
    public function removeVerifiedToken()
    {
        $this->verification_token = null;
    }

    /**
     * @return ActiveQuery
     */
    public function getTokens(): ActiveQuery
    {
        return $this->hasMany(Token::class, ['user_id' => 'id']);
    }
}
