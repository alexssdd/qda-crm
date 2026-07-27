<?php

namespace app\modules\auth\rbac;

use yii\rbac\Item;
use app\modules\auth\helpers\UserHelper;

final class RoleRegistry
{
    public static function items(): array
    {
        return [
            UserHelper::ROLE_ADMIN => self::role('Administrator', [
                UserHelper::ROLE_BOT,
                UserHelper::ROLE_ADMINISTRATOR,
            ]),
            UserHelper::ROLE_BOT => self::role('Bot'),
            UserHelper::ROLE_ADMINISTRATOR => self::role('Manager', [
                UserHelper::ROLE_OPERATOR,
                UserHelper::ROLE_MARKETING,
            ]),
            UserHelper::ROLE_OPERATOR => self::role('Sales operator'),
            UserHelper::ROLE_MARKETING => self::role('Marketing'),
            UserHelper::ROLE_SERVICE_VENDOR => self::role(UserHelper::ROLE_SERVICE_VENDOR),
            UserHelper::ROLE_SERVICE_PICKER => self::role(UserHelper::ROLE_SERVICE_PICKER),
            UserHelper::ROLE_SERVICE_POS => self::role(UserHelper::ROLE_SERVICE_POS),
            UserHelper::ROLE_SERVICE_WMS => self::role(UserHelper::ROLE_SERVICE_WMS),
            UserHelper::ROLE_SERVICE_DELIVERY => self::role(UserHelper::ROLE_SERVICE_DELIVERY),
            UserHelper::ROLE_SERVICE_TMS => self::role(UserHelper::ROLE_SERVICE_TMS),
            UserHelper::ROLE_SERVICE_JIVOSITE => self::role(UserHelper::ROLE_SERVICE_JIVOSITE),
            UserHelper::ROLE_SERVICE_TELEGRAM => self::role(UserHelper::ROLE_SERVICE_TELEGRAM),
            UserHelper::ROLE_SERVICE_KASPI => self::role(UserHelper::ROLE_SERVICE_KASPI),
            UserHelper::ROLE_SERVICE_WOLT => self::role(UserHelper::ROLE_SERVICE_WOLT),
            UserHelper::ROLE_SERVICE_GLOVO => self::role(UserHelper::ROLE_SERVICE_GLOVO),
            UserHelper::ROLE_SERVICE_YANDEX_EDA => self::role(UserHelper::ROLE_SERVICE_YANDEX_EDA),
        ];
    }

    private static function role(string $description, array $children = []): array
    {
        $role = [
            'type' => Item::TYPE_ROLE,
            'description' => $description,
        ];

        if ($children) {
            $role['children'] = $children;
        }

        return $role;
    }
}
