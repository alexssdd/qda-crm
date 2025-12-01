<?php
namespace app\modules\telegram;

use Yii;

trait ModuleTrait
{
    private $_module;

    public function getModule(): ?Module
    {
        if ($this->_module === null) {
            $this->_module = Yii::$app->getModule('telegram');
        }
        return $this->_module;
    }
}