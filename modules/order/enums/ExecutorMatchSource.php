<?php

namespace app\modules\order\enums;

/**
 * Источник матчинга исполнителя в событии executors.notified —
 * контракт с pro-инстансом (breakdown в data события).
 */
enum ExecutorMatchSource: string
{
    case SUBSCRIPTION = 'subscription';
    case PROFILE = 'profile';
    case ASSIGNED = 'assigned';

    public function getLabel(): string
    {
        return match ($this) {
            self::SUBSCRIPTION => 'по подписке',
            self::PROFILE => 'по профилю',
            self::ASSIGNED => 'адресный заказ',
        };
    }
}
