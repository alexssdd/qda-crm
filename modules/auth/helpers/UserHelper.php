<?php

namespace app\modules\auth\helpers;

use Yii;
use Exception;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use app\modules\auth\models\User;

/**
 * User helper
 */
class UserHelper
{
    /** Roles Users */
    const ROLE_ADMIN = 'admin';
    const ROLE_BOT = 'bot';
    const ROLE_ADMINISTRATOR = 'administrator';

    /** Statuses */
    const STATUS_ACTIVE = 10;
    const STATUS_INACTIVE = 11;
    const STATUS_DELETED = 12;

    /** Statuses */
    const STATE_ONLINE = 10;
    const STATE_OFFLINE = 11;

    static $user = null;

    /**
     * @return IdentityInterface|null|User
     */
    public static function getIdentity(): User|IdentityInterface|null
    {
        if (!self::$user){
            self::$user = Yii::$app->user->identity;
        }

        return self::$user;
    }

    /**
     * @return bool
     */
    public static function isAdmin(): bool
    {
        return UserHelper::getIdentity()->role == self::ROLE_ADMIN;
    }

    /**
     * @return bool
     */
    public static function isAdministrator(): bool
    {
        return UserHelper::getIdentity()->role == self::ROLE_ADMINISTRATOR;
    }

    /**
     * @return User|null
     * @throws Exception
     */
    public static function getBot(): ?User
    {
        $user = User::find()
            ->andWhere(['role' => self::ROLE_BOT])
            ->cache(60 * 60 * 24)
            ->one();

        if ($user === null) {
            throw new Exception('Bot user not found');
        }

        return $user;
    }

    /**
     * @param User $user
     * @return string
     */
    public static function getShortName(User $user): string
    {
        $parts = explode(' ', trim($user->full_name));

        if (count($parts) === 3) { // Ф.И.О
            return sprintf('%s %s.%s', $parts[0], mb_substr($parts[1], 0, 1), mb_substr($parts[2], 0, 1));
        }

        if (count($parts) === 2) { // Ф.И
            return sprintf('%s %s', $parts[0], mb_substr($parts[1], 0, 1));
        }

        return $user->full_name;
    }

    /**
     * @param User $user
     * @return mixed
     * @throws Exception
     */
    public static function getTelegramId(User $user): mixed
    {
        return ArrayHelper::getValue($user->config, 'telegram_id');
    }
}