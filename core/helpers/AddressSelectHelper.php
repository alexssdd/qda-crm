<?php

namespace app\core\helpers;

use Yii;
use yii\helpers\Html;

/**
 * Address select helper
 */
class AddressSelectHelper
{
    /** Types */
    const TYPE_MAP = 'map';
    const TYPE_INPUT = 'input';
    const TYPE_LIST = 'list';

    /**
     * @param $data
     * @return string|null
     */
    public static function getText($data): ?string
    {
        $result = Html::encode($data['address']);

        if ($data['entrance']){
            $result .= ', ' . mb_strtolower(Yii::t('app', 'Entrance'), 'UTF-8') . ' ' . Html::encode($data['entrance']);
        }
        if ($data['apartment']){
            $result .= ', ' . mb_strtolower(Yii::t('app', 'Apartment'), 'UTF-8') . ' ' . Html::encode($data['room']);
        }

        return $result;
    }

    /**
     * @param $address
     * @param $type
     * @param $title
     * @return string|null
     */
    public static function getLabel($address, $type, $title): ?string
    {
        $result = $address;
        
        if (!$type){
            return $result;
        }

        if ($type == self::TYPE_MAP){
            $result = Html::tag('strong', 'Указан на карте: ') . $address;
        } elseif ($type == self::TYPE_INPUT){
            $result = Html::tag('strong', 'Введен вручную: ') . $address;
        } elseif ($type == self::TYPE_LIST){
            $result = Html::tag('strong', 'Выбран из списка: ') . ($title ?: $address);
        }

        return $result;
    }
}