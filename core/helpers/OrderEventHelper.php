<?php

namespace app\core\helpers;

use Yii;
use Exception;
use yii\helpers\ArrayHelper;

/**
 * Order event helper
 */
class OrderEventHelper
{
    /** Common */
    const TYPE_BOT = 1;
    const TYPE_TRANSFER = 2;
    const TYPE_TRANSFER_ERROR = 3;
    const TYPE_MESSAGE = 5;
    const TYPE_CANCEL = 7;
    const TYPE_PENDING = 8;
    const TYPE_SIGNATURE_REQUIRED = 9;

    /** Assembly */
    const TYPE_ASSEMBLY_CREATED = 10;
    const TYPE_ASSEMBLY_ERROR = 11;
    const TYPE_ASSEMBLY_NOTIFY = 12;
    const TYPE_ASSEMBLY_PARTIAL = 13;
    const TYPE_ASSEMBLY_CONFIRMED = 14;
    const TYPE_ASSEMBLY_REMOVED = 15;

    /** Payment */
    const TYPE_PAYMENT_NEW = 20;
    const TYPE_PAYMENT_SUCCESS = 21;
    const TYPE_PAYMENT_FAILURE = 22;
    const TYPE_PAYMENT_WAIT = 23;
    const TYPE_PAYMENT_RETURN = 24;
    const TYPE_PAYMENT_PREPAID = 25;

    /** Store issued 30-40 */
    const TYPE_ORDER_ISSUED = 30;
    const TYPE_ORDER_ACTIVATE = 31;

    /** Pos */
    const TYPE_POS_COMPLETE = 40;
    const TYPE_POS_RETURN = 41;

    /** Znp */
    const TYPE_ZNP_CREATED = 50;
    const TYPE_ZNP_CREATED_ERROR = 51;
    const TYPE_ZNP_RECEIVED = 52;

    /** Kaspi */
    const TYPE_KASPI_CANCELLED = 60;
    const TYPE_KASPI_COMPLETED = 61;
    const TYPE_KASPI_CHANGE_QUANTITY = 62;
    const TYPE_KASPI_CHANGE_QUANTITY_ERROR = 63;
    const TYPE_KASPI_SAVE_WAYBILL = 64;
    const TYPE_KASPI_SAVE_WAYBILL_ERROR = 65;

    /** Jusan */
    const TYPE_JUSAN_CANCELLED = 70;
    const TYPE_JUSAN_COMPLETED = 71;
    const TYPE_JUSAN_CHANGE_QUANTITY = 72;
    const TYPE_JUSAN_CHANGE_QUANTITY_ERROR = 73;

    /** Halyk */
    const TYPE_HALYK_COMPLETED = 80;
    const TYPE_HALYK_CANCELLED = 81;

    /** Ozon */
    const TYPE_OZON_WAYBILL = 82;
    const TYPE_OZON_CANCELLED = 83;
    const TYPE_OZON_DELIVERED = 84;

    /** Wb */
    const TYPE_WB_CANCELLED = 90;
    const TYPE_WB_COMPLETED = 91;
    const TYPE_WB_SAVE_WAYBILL = 92;
    const TYPE_WB_SAVE_WAYBILL_ERROR = 93;
	const TYPE_YANDEX_EXPRESS_TRANSFER = 94;

    /** Forte */
    const TYPE_FORTE_CANCELLED = 100;
    const TYPE_FORTE_COMPLETED = 101;

    /** SL */
    const TYPE_SL_COMPLETE = 110;
    const TYPE_SL_COMPLETE_ERROR = 111;
    const TYPE_SL_CANCEL = 112;
    const TYPE_SL_CANCEL_ERROR = 113;
    const TYPE_SL_REPLACE = 114;

    /** Picker */
    const TYPE_PICKER_ASSEMBLY = 150;
    const TYPE_PICKER_COMPLETE = 151;
    const TYPE_PICKER_REMOVE = 152;

    /** Bonus */
    const TYPE_BONUS_DISTRIBUTE = 160;

    /** Notify (170 - 199) */
    const TYPE_NOTIFY_PICKUP_READY = 170;
    const TYPE_NOTIFY_CANCELLED = 171;

    /** Telegram (200 - 219) */
    const TYPE_TG_KASPI_DELIVERED = 200;

    /** Correct */
    const TYPE_CORRECT_SUCCESS = 220;

    /** Return */
    const TYPE_ORDER_RETURN_CANCELED = 230;

    /** Move */
    const TYPE_MOVE_SUCCESS = 240;

	/** Emex */
	const TYPE_EMEX_CREATE = 300;
	const TYPE_EMEX_CANCEL = 301;
	const TYPE_EMEX_STATUS = 302;
	const TYPE_EMEX_OTHER = 303;
	const TYPE_EMEX_PDF = 304;

    /**
     * @return array
     */
    public static function getTypeArray(): array
    {
        return [
            self::TYPE_TRANSFER => Yii::t('order', 'TYPE_TRANSFER'),
        ];
    }

    /**
     * @param $type
     * @return string|null
     * @throws Exception
     */
    public static function getIconClass($type): ?string
    {
        $data = [
            self::TYPE_TRANSFER => 'icon-arrow_forward',
            self::TYPE_TRANSFER_ERROR => 'icon-warning',
            self::TYPE_ORDER_ISSUED => 'icon-error_outline',
            self::TYPE_ASSEMBLY_NOTIFY => 'icon-warning',
            self::TYPE_ASSEMBLY_CONFIRMED => 'icon-error_outline',
            self::TYPE_POS_COMPLETE => 'icon-analytics',
            self::TYPE_POS_RETURN => 'icon-analytics',
        ];

        return ArrayHelper::getValue($data, $type);
    }

    /**
     * @param $type
     * @return string|null
     * @throws Exception
     */
    public static function getImage($type): ?string
    {
        $data = [
            // Kaspi
            self::TYPE_KASPI_CANCELLED => '/images/logo_kaspi.svg',
            self::TYPE_KASPI_COMPLETED => '/images/logo_kaspi.svg',
            self::TYPE_KASPI_CHANGE_QUANTITY => '/images/logo_kaspi.svg',
            self::TYPE_KASPI_CHANGE_QUANTITY_ERROR => '/images/logo_kaspi.svg',
            self::TYPE_KASPI_SAVE_WAYBILL => '/images/logo_kaspi.svg',
            self::TYPE_KASPI_SAVE_WAYBILL_ERROR => '/images/logo_kaspi.svg',

            // Jusan
            self::TYPE_JUSAN_CANCELLED => '/images/logo_jusan.svg',
            self::TYPE_JUSAN_COMPLETED => '/images/logo_jusan.svg',
            self::TYPE_JUSAN_CHANGE_QUANTITY => '/images/logo_jusan.svg',
            self::TYPE_JUSAN_CHANGE_QUANTITY_ERROR => '/images/logo_jusan.svg',

            // Halyk
            self::TYPE_HALYK_COMPLETED => '/images/logo_halyk.svg',
            self::TYPE_HALYK_CANCELLED => '/images/logo_halyk.svg',

            // Ozon
            self::TYPE_OZON_WAYBILL => '/images/logo_ozon.svg',

            // WB
            self::TYPE_WB_CANCELLED => '/images/logo_wb.svg',
            self::TYPE_WB_COMPLETED => '/images/logo_wb.svg',
            self::TYPE_WB_SAVE_WAYBILL => '/images/logo_wb.svg',
            self::TYPE_WB_SAVE_WAYBILL_ERROR => '/images/logo_wb.svg',

            // Forte
            self::TYPE_FORTE_CANCELLED => '/images/logo_forte.svg',
            self::TYPE_FORTE_COMPLETED => '/images/logo_forte.svg',

            self::TYPE_TG_KASPI_DELIVERED => '/images/logo_tg.svg'
        ];

        return ArrayHelper::getValue($data, $type);
    }

    /**
     * @param $type
     * @return string
     */
    public static function getPriorityClass($type): string
    {
        $classArray = [];
        $importantTypes = [
            self::TYPE_ASSEMBLY_ERROR,
            self::TYPE_ASSEMBLY_NOTIFY,
            self::TYPE_ASSEMBLY_PARTIAL,
            self::TYPE_PAYMENT_WAIT,

        ];
        $successfulTypes = [
            self::TYPE_ORDER_ISSUED,
            self::TYPE_POS_COMPLETE,
            self::TYPE_CORRECT_SUCCESS,
            self::TYPE_PAYMENT_SUCCESS,
            self::TYPE_PAYMENT_PREPAID,
            self::TYPE_PAYMENT_RETURN,
        ];
        $dangerTypes = [
            self::TYPE_ZNP_CREATED_ERROR,
            self::TYPE_KASPI_SAVE_WAYBILL_ERROR,
            self::TYPE_PAYMENT_FAILURE
        ];
        $infoTypes = [
            self::TYPE_POS_RETURN
        ];

        if (in_array($type, $importantTypes)){
            $classArray[] = 'order-chat__item--important';
        }
        if (in_array($type, $successfulTypes)){
            $classArray[] = 'order-chat__item--successful';
        }
        if (in_array($type, $dangerTypes)){
            $classArray[] = 'order-chat__item--danger';
        }
        if (in_array($type, $infoTypes)){
            $classArray[] = 'order-chat__item--info';
        }

        return implode(' ', $classArray);
    }

    /**
     * @return string[]
     */
    public static function getFilterTypes(): array
    {
        return [
            self::TYPE_ZNP_CREATED_ERROR => 'Ошибка при создании ЗНП',
            self::TYPE_KASPI_CHANGE_QUANTITY_ERROR => 'Ошибка при изменении количество товаров в Kaspi Shop',
            self::TYPE_KASPI_SAVE_WAYBILL_ERROR => 'Ошибка при скачивании накладной в Kaspi Shop',
            self::TYPE_JUSAN_CHANGE_QUANTITY_ERROR => 'Ошибка при изменении количество товаров в Jusan',
            self::TYPE_WB_SAVE_WAYBILL_ERROR => 'Ошибка при скачивании накладной в Wildberries',
            self::TYPE_ASSEMBLY_ERROR => 'Ошибка при сборке',
        ];
    }
}