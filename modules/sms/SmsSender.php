<?php

namespace app\modules\sms;

interface SmsSender
{
    public function send($phone, $message);
}