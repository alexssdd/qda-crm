<?php

namespace app\core\security;

use Yii;

/**
 * Account-based password attempt limiter.
 *
 * The key deliberately does not depend on IP because the CRM can run behind Cloudflare.
 */
final class LoginAttemptLimiter
{
    private const MAX_ATTEMPTS = 10;
    private const WINDOW = 900;

    public static function isBlocked(string $identifier): bool
    {
        $data = self::getData($identifier);

        return $data['count'] >= self::MAX_ATTEMPTS;
    }

    public static function registerFailure(string $identifier): void
    {
        $data = self::getData($identifier);
        $data['count']++;

        Yii::$app->cache->set(self::getKey($identifier), $data, self::WINDOW);
    }

    public static function clear(string $identifier): void
    {
        Yii::$app->cache->delete(self::getKey($identifier));
    }

    private static function getData(string $identifier): array
    {
        $data = Yii::$app->cache->get(self::getKey($identifier));
        if (!is_array($data) || !isset($data['count'])) {
            return ['count' => 0];
        }

        return ['count' => max(0, (int) $data['count'])];
    }

    private static function getKey(string $identifier): string
    {
        return 'crm_password_attempt_' . hash('sha256', $identifier);
    }
}
