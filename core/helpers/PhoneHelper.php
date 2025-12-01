<?php

namespace app\core\helpers;

/**
 * Phone helper
 */
class PhoneHelper
{
    /**
     * @param $string
     * @return array|string|string[]|null
     */
    public static function getCleanNumber($string)
    {
        if (!$string){
            return '';
        }
        return preg_replace("/[^0-9]/", '', $string);
    }

    /**
     * @param $string
     * @return string
     */
    public static function getMaskPhone($string): string
    {
        $data = '+' . $string;

        if(preg_match( '/^\+\d(\d{3})(\d{3})(\d{2})(\d{2})$/', $data,  $matches )) {
            return '+7(' . $matches[1] . ')' .$matches[2] . '-' . $matches[3] . '-' .$matches[4];
        }

        return $string;
    }
}