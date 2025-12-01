<?php

namespace app\modules\sms\jobs;

use Exception;
use yii\base\BaseObject;
use app\modules\sms\providers\SmsC;
use yii\queue\RetryableJobInterface;
use app\modules\sms\services\SmsService;

class SmsJob extends BaseObject implements RetryableJobInterface
{
    public $phone;
    public $message;

    /**
     * @param $queue
     * @return void
     * @throws Exception
     */
    public function execute($queue)
    {
        (new SmsService(new SmsC()))->send($this->phone, $this->message);
    }

    /**
     * @return int
     */
    public function getTtr(): int
    {
        return 60;
    }

    /**
     * @param $attempt
     * @param $error
     * @return bool
     */
    public function canRetry($attempt, $error): bool
    {
        return $attempt < 2;
    }
}