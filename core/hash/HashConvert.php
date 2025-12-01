<?php

namespace app\core\hash;

/**
 * HashConvert
 */
class HashConvert
{
    protected static $base = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * @param $value
     * @return string
     */
    public function make($value): string
    {
        $b = 82;
        $r = $value % $b;
        $result = static::$base[$r];
        $q = floor($value / $b);

        while ($q) {
            $r = $q % $b;
            $q = floor($q / $b);
            $result = static::$base[$r].$result;
        }
        return $result;
    }

    /**
     * @param $value
     * @return string
     */
    public function extract($value): string
    {
        $b = 82;
        $limit = strlen($value);
        $result = strpos(static::$base, $value[0]);

        for($i = 1; $i < $limit; $i++) {
            $result = $b * $result + strpos(static::$base, $value[$i]);
        }
        return $result;
    }
}