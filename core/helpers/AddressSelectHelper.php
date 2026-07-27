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
            $result .= ', ' . mb_strtolower(Yii::t('app', 'Apartment'), 'UTF-8') . ' ' . Html::encode($data['apartment']);
        }

        return $result;
    }

    /**
     * @param $address
     * @param $type
     * @param $title
     * @return string|null
     */
    public static function getLabel($address, $type, $title): string
    {
        return $type == self::TYPE_LIST && $title
            ? (string) $title
            : (string) $address;
    }

    public static function getLabelPrefix($type): string
    {
        return match ($type) {
            self::TYPE_MAP => 'Указан на карте: ',
            self::TYPE_INPUT => 'Введен вручную: ',
            self::TYPE_LIST => 'Выбран из списка: ',
            default => '',
        };
    }
}
