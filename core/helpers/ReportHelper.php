<?php

namespace app\core\helpers;

use Yii;
use Exception;
use yii\helpers\Html;
use app\entities\Report;
use yii\helpers\FileHelper;
use yii\helpers\ArrayHelper;

/**
 * Report helper
 */
class ReportHelper
{
    /** Types */
    const TYPE_ORDER = 10;
    const TYPE_DEFECTURA = 11;
    const TYPE_CARE = 12;
    const TYPE_LEAD = 13;

    /** Status */
    const STATUS_PROCESS = 10;
    const STATUS_DONE = 11;
    const STATUS_ERROR = 12;

    /**
     * @return array
     */
    public static function getStatusArray(): array
    {
        return [
            self::STATUS_PROCESS => Yii::t('app', 'STATUS_PROCESS'),
            self::STATUS_DONE => Yii::t('app', 'STATUS_DONE'),
            self::STATUS_ERROR => Yii::t('app', 'STATUS_ERROR'),
        ];
    }

    /**
     * @param $status
     * @return mixed|null
     * @throws Exception
     */
    public static function getStatusName($status)
    {
        return ArrayHelper::getValue(self::getStatusArray(), $status);
    }

    /**
     * @param $status
     * @return string
     * @throws Exception
     */
    public static function getStatusLabel($status): string
    {
        $keys = [
            self::STATUS_PROCESS => 'info',
            self::STATUS_DONE => 'success',
            self::STATUS_ERROR => 'danger',
        ];

        return Html::tag('span', self::getStatusName($status), [
            'class' => 'label label-' . ArrayHelper::getValue($keys, $status, 'default')
        ]);
    }

    /**
     * @return bool|false|string
     */
    public static function getFolder()
    {
        return Yii::getAlias('@storage/private/report/');
    }

    /**
     * @param Report $report
     * @return string
     * @throws Exception
     */
    public static function getFilePath(Report $report): string
    {
        // Prepare path
        $path = ReportHelper::getFolder() . $report->user_id;
        FileHelper::createDirectory($path);

        // Prepare file
        return $path . '/' . $report->id . '.xlsx';
    }
}