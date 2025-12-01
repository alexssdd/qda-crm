<?php

namespace app\core\validators;

use yii\validators\RegularExpressionValidator;

class PhoneValidator extends RegularExpressionValidator
{
    public $pattern = '#^\+7\(\d{3}\)\d{3}\-\d{2}\-\d{2}#is';
}