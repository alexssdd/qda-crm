<?php

namespace app\services;

use Yii;
use Exception;
use app\entities\Log;
use app\core\helpers\LogHelper;

/**
 * Log service
 */
class LogService
{
    /**
     * @param $target
     * @param $data
     * @param $start
     * @param bool $reconnect
     * @return void
     * @throws Exception
     */
    public static function success($target, $data, $start = null, bool $reconnect = false): void
    {
        static::create($target, LogHelper::STATUS_SUCCESS, $data, $start ?: time(), $reconnect);
    }

    /**
     * @param $target
     * @param $data
     * @param $start
     * @param bool $reconnect
     * @return void
     * @throws Exception
     */
    public static function error($target, $data, $start = null, bool $reconnect = false): void
    {
        static::create($target, LogHelper::STATUS_ERROR, $data, $start ?: time(), $reconnect);
    }

    /**
     * @param $target
     * @param $status
     * @param $data
     * @param $start
     * @param bool $reconnect
     * @return void
     * @throws Exception
     */
    public static function create($target, $status, $data, $start, bool $reconnect = false): void
    {
        if ($reconnect) {
            Yii::$app->db->close(); // todo fix mysql has gone away
        }

        $model = new Log();
        $model->target = $target;
        $model->data = $data;
        $model->runtime = microtime(true) - $start;
        $model->created_at = time();
        $model->status = $status;
        $model->save();
    }
}
