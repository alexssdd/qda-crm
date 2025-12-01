<?php

namespace app\modules\sms\services;

use Exception;
use app\services\LogService;
use app\modules\sms\SmsSender;
use app\core\helpers\LogHelper;

class SmsService
{
    private SmsSender $_sender;

    /**
     * @param SmsSender $sender
     */
    public function __construct(SmsSender $sender)
    {
        $this->_sender = $sender;
    }

    /**
     * @param $phone
     * @param $message
     * @return void
     * @throws Exception
     */
    public function send($phone, $message): void
    {
        $target = LogHelper::TARGET_SMS_CREATE;
        try {
            $response = $this->_sender->send($phone, $message);

            LogService::success($target, ['phone' => $phone, 'message' => $message, 'response' => $response]);
        } catch (Exception $e) {
            LogService::error($target, ['phone' => $phone, 'message' => $message, 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}