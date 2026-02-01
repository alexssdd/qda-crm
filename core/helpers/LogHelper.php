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

    // Order: Sync and automation
    const TARGET_ORDER_SYNC = 'order_sync';
    const TARGET_ORDER_GENERATE_NUMBER = 'order_generate_number';
    const TARGET_ORDER_BOT = 'order_bot';
    const TARGET_ORDER_ASSEMBLY_CONTINUE = 'order_assembly_continue';
    const TARGET_ORDER_RESERVE_CREATE = 'order_reserve_create';
    const TARGET_ORDER_ASSEMBLY_MOVE = 'order_assembly_move';

    // SMS: Notifications
    const TARGET_SMS_CREATE = 'sms_create';

    // Payment: Online payments
    const TARGET_PAYMENT_WIDGET = 'payment_widget';

    // Telegram: Bot interactions
    const TARGET_TELEGRAM_SEND = 'telegram_send';
    const TARGET_TELEGRAM_CALLBACK = 'telegram_callback';
    const TARGET_TELEGRAM_INVITE = 'telegram_invite';
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

            // Order
            self::TARGET_ORDER_SYNC => 'ORDER_SYNC',
            self::TARGET_ORDER_GENERATE_NUMBER => 'ORDER_GENERATE_NUMBER',
            self::TARGET_ORDER_BOT => 'ORDER_BOT',
            self::TARGET_ORDER_ASSEMBLY_CONTINUE => 'ORDER_ASSEMBLY_CONTINUE',
            self::TARGET_ORDER_RESERVE_CREATE => 'ORDER_RESERVE_CREATE',
            self::TARGET_ORDER_ASSEMBLY_MOVE => 'ORDER_ASSEMBLY_MOVE',

            // SMS
            self::TARGET_SMS_CREATE => 'SMS_CREATE',

            // Payment
            self::TARGET_PAYMENT_WIDGET => 'PAYMENT_WIDGET',

            // Telegram
            self::TARGET_TELEGRAM_SEND => 'TELEGRAM_SEND',
            self::TARGET_TELEGRAM_CALLBACK => 'TELEGRAM_CALLBACK',
            self::TARGET_TELEGRAM_INVITE => 'TELEGRAM_INVITE',
            self::TARGET_TELEGRAM_ORDER_TRANSFER => 'TELEGRAM_ORDER_TRANSFER',
            self::TARGET_TELEGRAM_ORDER_KASPI_DELIVERED => 'TELEGRAM_ORDER_KASPI_DELIVERED',
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