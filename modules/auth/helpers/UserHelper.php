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
    const ROLE_OPERATOR = 'operator';
    const ROLE_MARKETING = 'marketing';

    /** Service roles */
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

    public static function isCrmLoginRole(?string $role): bool
    {
        return in_array($role, [
            self::ROLE_ADMIN,
            self::ROLE_ADMINISTRATOR,
            self::ROLE_OPERATOR,
            self::ROLE_MARKETING,
        ], true);
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

    public static function getRoleArray(): array
    {
        $roles = self::getCreateRoleArray();

        if (self::isAdmin()){
            return [self::ROLE_ADMIN => Yii::t('app', 'ROLE_ADMIN')] + $roles;
        }

        return $roles;
    }

    public static function getCreateRoleArray(): array
    {
        if (self::isAdmin()){
            return [
                self::ROLE_ADMINISTRATOR => Yii::t('app', 'ROLE_ADMINISTRATOR'),
                self::ROLE_OPERATOR => Yii::t('app', 'ROLE_OPERATOR'),
                self::ROLE_MARKETING => Yii::t('app', 'ROLE_MARKETING'),
            ];
        }

        return [
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

    public static function getRoleName($role): string
    {
        return (string) ArrayHelper::getValue(self::getRoleArrayAll(), $role, $role);
    }

    public static function getStatusArray(): array
    {
        return [
            self::STATUS_ACTIVE => Yii::t('app', 'STATUS_ACTIVE'),
            self::STATUS_INACTIVE => Yii::t('app', 'STATUS_INACTIVE'),
            self::STATUS_DELETED => Yii::t('app', 'STATUS_DELETED'),
        ];
    }

    public static function getStatusLabel(int $status): string
    {
        $class = $status === self::STATUS_ACTIVE
            ? 'label label-success'
            : 'label label-danger';

        return Html::tag(
            'span',
            (string) ArrayHelper::getValue(self::getStatusArray(), $status, $status),
            ['class' => $class]
        );
    }

    public static function getSelectArray(): array
    {
        $users = User::find()
            ->andWhere(['status' => self::STATUS_ACTIVE])
            ->andWhere(['role' => [
                self::ROLE_OPERATOR,
                self::ROLE_BOT,
                self::ROLE_ADMINISTRATOR,
                self::ROLE_ADMIN,
            ]])
            ->orderBy(['name' => SORT_ASC])
            ->cache(60 * 5)
            ->all();

        $result = [];
        foreach ($users as $user) {
            $roleName = self::getRoleName($user->role);
            if (!array_key_exists($roleName, $result)) {
                $result[$roleName] = [];
            }

            $result[$roleName][$user->id] = $user->name;
        }

        return $result;
    }

    public static function getShortName(User $user): string
    {
        $parts = explode(' ', trim($user->name));

        if (count($parts) === 3) { // Ф.И.О
            return sprintf('%s %s.%s', $parts[0], mb_substr($parts[1], 0, 1), mb_substr($parts[2], 0, 1));
        }

        if (count($parts) === 2) { // Ф.И
            return sprintf('%s %s', $parts[0], mb_substr($parts[1], 0, 1));
        }

        return $user->name;
    }
}
