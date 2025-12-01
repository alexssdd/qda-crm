<?php

namespace app\core;

/**
 * Active record
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * @return mixed
     */
    public function getErrorMessage(): mixed
    {
        return $this->getErrorSummary(true)[0];
    }
}