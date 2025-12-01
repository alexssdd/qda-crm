<?php

namespace app\modules\mail;

use Yii;
use Exception;
use app\services\ConfigService;
use app\core\helpers\ConfigHelper;

/**
 * Class Module
 * @package app\modules\mail
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\mail\controllers';

    private ConfigService $_config;

    /**
     * @return void
     */
    public function init(): void
    {
        parent::init();

        $this->_config = new ConfigService();

        if (Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\mail\commands';
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSmtpHost(): string
    {
        return $this->_config->getRequired(ConfigHelper::KEY_MAIL, 'smtp_host');
    }

    /**
     * @return int
     */
    public function getSmtpPort(): int
    {
        return $this->_config->getRequired(ConfigHelper::KEY_MAIL, 'smtp_port');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSmtpUser(): string
    {
        return $this->_config->getRequired(ConfigHelper::KEY_MAIL, 'smtp_user');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getSmtpPassword(): string
    {
        return $this->_config->getRequired(ConfigHelper::KEY_MAIL, 'smtp_password');
    }
}