<?php
namespace app\modules\sms;

use Yii;
use yii\base\Module;

trait ModuleTrait
{
    private $_module;

    public function getModule(): ?Module
    {
        if ($this->_module === null) {
            $this->_module = Yii::$app->getModule('sms');
        }
        return $this->_module;
    }
}