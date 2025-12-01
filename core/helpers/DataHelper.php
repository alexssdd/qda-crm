<?php

namespace app\core\helpers;

use Yii;
use Exception;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * Data helper
 */
class DataHelper
{
    const BOOL_YES = 10;
    const BOOL_NO = 11;

    /**
     * @return array
     */
    public static function getBoolArray(): array
    {
        return [
            self::BOOL_YES => Yii::t('app', 'BOOL_YES'),
            self::BOOL_NO => Yii::t('app', 'BOOL_NO'),
        ];
    }

    /**
     * @param $bool
     * @return mixed
     * @throws Exception
     */
    public static function getBoolName($bool)
    {
        return ArrayHelper::getValue(self::getBoolArray(), $bool);
    }

    /**
     * @param $bool
     * @return string
     * @throws Exception
     */
    public static function getBoolLabel($bool): string
    {
        $data = [
            self::BOOL_YES => 'success',
            self::BOOL_NO => 'danger',
        ];

        return Html::tag('span', self::getBoolName($bool), [
            'class' => 'label label-' . ArrayHelper::getValue($data, $bool, 'default'),
        ]);
    }
}