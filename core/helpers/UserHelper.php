<?php

namespace app\core\helpers;

use Yii;
use Exception;
use yii\helpers\Html;
use app\entities\User;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * User helper
 */
class UserHelper
{
    /** Roles Users */
    const ROLE_ADMIN = 'admin';
    const ROLE_BOT = 'bot';
    const ROLE_ADMINISTRATOR = 'administrator';
    const ROLE_OPERATOR = 'operator';
    const ROLE_MARKETING = 'marketing';

    /** Roles Services */
    const ROLE_SERVICE_VENDOR = 'service_vendor';
    const ROLE_SERVICE_PICKER = 'service_picker';
    const ROLE_SERVICE_POS = 'service_pos';
    const ROLE_SERVICE_WMS = 'service_wms';
    const ROLE_SERVICE_DELIVERY = 'service_delivery';
    const ROLE_SERVICE_TMS = 'service_tms';
    const ROLE_SERVICE_JIVOSITE = 'service_jivosite';
    const ROLE_SERVICE_TELEGRAM = 'service_telegram';
    const ROLE_SERVICE_KASPI = 'service_kaspi';
    const ROLE_SERVICE_WOLT = 'service_wolt';
    const ROLE_SERVICE_GLOVO = 'service_glovo';
    const ROLE_SERVICE_YANDEX_EDA = 'service_yandex_eda';

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
    public static function getIdentity()
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
     * @return bool
     */
    public static function isOperator(): bool
    {
        return UserHelper::getIdentity()->role == self::ROLE_OPERATOR;
    }

    /**
     * @return string[]
     */
    public static function getDashboardRoles(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_ADMINISTRATOR,
            self::ROLE_OPERATOR,
            self::ROLE_MARKETING,
        ];
    }

    /**
     * @return array status labels indexed by status values
     */
    public static function getStatusArray(): array
    {
        return [
            self::STATUS_ACTIVE => Yii::t('app', 'STATUS_ACTIVE'),
            self::STATUS_INACTIVE => Yii::t('app', 'STATUS_INACTIVE'),
            self::STATUS_DELETED => Yii::t('app', 'STATUS_DELETED')
        ];
    }

    /**
     * @param $status
     * @return string
     * @throws Exception
     */
    public static function getStatusLabel($status): string
    {
        switch ($status) {
            case self::STATUS_ACTIVE:
                $class = 'label label-success';
                break;
            case self::STATUS_INACTIVE:
            case self::STATUS_DELETED:
                $class = 'label label-danger';
                break;
            default:
                $class = 'label label-default';
        }

        return Html::tag('span', ArrayHelper::getValue(self::getStatusArray(), $status), [
            'class' => $class,
        ]);
    }

    /**
     * @return array
     */
    public static function getStateArray(): array
    {
        return [
            self::STATE_ONLINE => Yii::t('app', 'STATE_ONLINE'),
            self::STATE_OFFLINE => Yii::t('app', 'STATE_OFFLINE')
        ];
    }

    /**
     * @return string[]
     */
    public static function getRoleArray(): array
    {
        if (self::isAdmin()){
            return self::getRoleArrayAll();
        }

        return [
            self::ROLE_ADMINISTRATOR => Yii::t('app', 'ROLE_ADMINISTRATOR'),
            self::ROLE_OPERATOR => Yii::t('app', 'ROLE_OPERATOR'),
            self::ROLE_MARKETING => Yii::t('app', 'ROLE_MARKETING'),
        ];
    }

    /**
     * @return string[]
     */
    public static function getRoleArrayAll(): array
    {
        return [
            self::ROLE_ADMIN => Yii::t('app', 'ROLE_ADMIN'),
            self::ROLE_BOT => Yii::t('app', 'ROLE_BOT'),
            self::ROLE_ADMINISTRATOR => Yii::t('app', 'ROLE_ADMINISTRATOR'),
            self::ROLE_OPERATOR => Yii::t('app', 'ROLE_OPERATOR'),
            self::ROLE_MARKETING => Yii::t('app', 'ROLE_MARKETING'),
        ];
    }

    /**
     * @param $role
     * @return mixed
     * @throws Exception
     */
    public static function getRoleName($role): string
    {
        return ArrayHelper::getValue(self::getRoleArrayAll(), $role);
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
     * @return array
     * @throws Exception
     */
    public static function getSelectArray(): array
    {
        /** @var User[] $users */
        $users = User::find()
            ->andWhere(['status' => self::STATUS_ACTIVE])
            ->andWhere(['role' => [self::ROLE_OPERATOR, self::ROLE_BOT, self::ROLE_ADMINISTRATOR, self::ROLE_ADMIN]])
            ->orderBy(['full_name' => SORT_ASC])
            ->cache(60 * 5)
            ->all();

        $result = [];
        foreach ($users as $user) {
            $roleName = UserHelper::getRoleName($user->role);
            if (!array_key_exists($roleName, $result)){
                $result[$roleName] = [];
            }

            $result[$roleName][$user->id] = $user->full_name;
        }

        return $result;
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