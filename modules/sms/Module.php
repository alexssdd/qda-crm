<?php

namespace app\modules\sms;

use Yii;
use DomainException;
use app\entities\Config;
use yii\helpers\ArrayHelper;
use app\core\helpers\ConfigHelper;
use yii\helpers\VarDumper;

/**
 * Class Module
 * @package app\modules\sms
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\sms\controllers';

    private $_config;

    public function init()
    {
        parent::init();

        if (Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\sms\commands';
        }
    }

    public function getApiUrl()
    {
        if (!$url = ArrayHelper::getValue($this->getConfig()->values, 'url')) {
            throw new DomainException("Url not set");
        }
        return $url;
    }

    public function getApiAuth(): array
    {
        $auth = ArrayHelper::getValue($this->getConfig()->values, 'auth');

        if (!$auth['login'] || !$auth['password']) {
            throw new DomainException("Auth login or password not set");
        }

        return $auth;
    }

    protected function getConfig(): ?Config
    {
        if ($this->_config === null) {

        }
        return $this->_config;
    }
}