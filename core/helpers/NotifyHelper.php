<?php

namespace app\core\helpers;

use Exception;
use yii\helpers\ArrayHelper;

/**
 * Notify helper
 */
class NotifyHelper
{
    /** Providers */
    const PROVIDER_WHATSAPP = 'whatsapp';
    const PROVIDER_SMS = 'sms';
    const PROVIDER_MAIL = 'mail';

    /**
     * @param $provider
     * @return mixed
     * @throws Exception
     */
    public static function getProviderName($provider): mixed
    {
        $data = [
            self::PROVIDER_WHATSAPP => 'Whatsapp',
            self::PROVIDER_SMS => 'SMS',
            self::PROVIDER_MAIL => 'Почта'
        ];

        return ArrayHelper::getValue($data, $provider);
    }
}