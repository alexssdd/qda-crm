<?php

namespace app\modules\telegram;

use Yii;
use DomainException;
use app\entities\Config;
use yii\helpers\ArrayHelper;
use app\core\helpers\ConfigHelper;

/**
 * Class Module
 * @package app\modules\tms
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\telegram\controllers';

    private $_config;

    public function init()
    {
        parent::init();

        if (Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\telegram\commands';
        }
    }

    public function getId(): string
    {
        if (!$id = ArrayHelper::getValue($this->getConfig()->values, 'id')) {
            throw new DomainException("Id not set");
        }

        return $id;
    }

    protected function getConfig(): ?Config
    {
        if ($this->_config === null) {
            //
        }
        return $this->_config;
    }
}