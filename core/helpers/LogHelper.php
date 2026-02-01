<?php

namespace app\core\helpers;

use Yii;
use Exception;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * Log helper
 */
class LogHelper
{
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;

    const TARGET_REQUEST = 'request';

    // Client
    const TARGET_CLIENT_ORDER_CREATE = 'client.order.create';
    const TARGET_CLIENT_ORDER_STATUS = 'client.order.status';

    // Pro
    const TARGET_PRO_BID = 'pro.create';

    // Order
    const TARGET_ORDER_NUMBER_GENERATE = 'order.number.generate';

    // SMS: Notifications
    const TARGET_SMS_CREATE = 'sms_create';

    // Telegram: Bot interactions
    const TARGET_TELEGRAM_SEND = 'telegram_send';
    const TARGET_TELEGRAM_CALLBACK = 'telegram_callback';
    const TARGET_TELEGRAM_ORDER_TRANSFER = 'telegram_order_transfer';
    const TARGET_TELEGRAM_ORDER_KASPI_DELIVERED = 'telegram_order_kaspi_delivered';

    // Module Mail
    const TARGET_MAIL_SEND_MESSAGE = 'mail_send_message';

    /**
     * @return array
     */
    public static function statusList(): array
    {
        return [
            self::STATUS_SUCCESS => Yii::t('app', 'STATUS_SUCCESS'),
            self::STATUS_ERROR => Yii::t('app', 'STATUS_ERROR'),
        ];
    }

    /**
     * @return string[]
     */
    public static function targetList(): array
    {
        return [
            self::TARGET_REQUEST => 'REQUEST',
            self::TARGET_CLIENT_ORDER_CREATE => 'CLIENT_ORDER_CREATE',
            self::TARGET_CLIENT_ORDER_STATUS => 'CLIENT_ORDER_STATUS',
            self::TARGET_PRO_BID => 'PRO_BID',
        ];
    }

    /**
     * @param $target
     * @return mixed
     * @throws Exception
     */
    public static function targetName($target)
    {
        return ArrayHelper::getValue(self::targetList(), $target);
    }

    /**
     * @param $status
     * @return string
     * @throws Exception
     */
    public static function statusLabel($status): string
    {
        switch ($status) {
            case self::STATUS_SUCCESS:
                $class = 'label label-success';
                break;
            case self::STATUS_ERROR:
                $class = 'label label-danger';
                break;
            default:
                $class = 'label label-default';
        }

        return Html::tag('span', ArrayHelper::getValue(self::statusList(), $status), [
            'class' => $class,
        ]);
    }

    /**
     * @param Exception $e
     * @return string
     */
    public static function getExtendedText(Exception $e): string
    {
        return 'File: ' . $e->getFile() . '. Line: ' . $e->getLine();
    }
}