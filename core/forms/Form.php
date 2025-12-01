<?php

namespace app\core\forms;

use yii\base\Model;

class Form extends Model
{
    public function getErrorMessage()
    {
        return $this->getErrorSummary(true)[0];
    }
}