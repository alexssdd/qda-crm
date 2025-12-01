<?php

namespace app\core\validators;

use app\core\helpers\PhoneHelper;

class PhoneMaskValidator
{
    public static function validateMask($value): bool
    {
        $number = PhoneHelper::getCleanNumber($value);
        $firstNumber = substr($number, 1, 1);

        if ($firstNumber !== '7') {
            return false;
        }
        return true;
    }
}