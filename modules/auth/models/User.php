<?php

namespace app\modules\auth\models;

use Yii;
use Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use app\modules\auth\helpers\UserHelper;

/**
 * User model
 *
 * @property int $id
 * @property string $role
 * @property string $phone
 * @property string $country
 * @property string $name
 * @property int $created_at
 * @property int $status
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id): User|IdentityInterface|null
    {
        return static::findOne(['id' => $id, 'status' => UserHelper::STATUS_ACTIVE]);
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
    public function getAuthKey(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return false;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }
}
