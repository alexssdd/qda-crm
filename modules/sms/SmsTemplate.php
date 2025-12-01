<?php

namespace app\modules\sms;

class SmsTemplate
{
    public static function orderPay($link): string
    {
        return "Ссылка для оплаты: $link";
    }
}