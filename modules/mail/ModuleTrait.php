<?php

namespace app\modules\mail;

use Yii;

/**
 * Module trait
 */
trait ModuleTrait
{
    private $_module;

    /**
     * @return Module|null
     */
    public function getModule(): ?Module
    {
        if ($this->_module === null) {
            $this->_module = Yii::$app->getModule('mail');
        }

        return $this->_module;
    }
}