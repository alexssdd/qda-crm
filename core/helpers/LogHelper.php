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

    // Vendor: Product sync & pricing
    const TARGET_VENDOR_STOCKS_LOAD = 'vendor_stocks_load';
    const TARGET_VENDOR_PRODUCTS_IMPORT = 'vendor_products_import';
    const TARGET_VENDOR_PRODUCTS_INSERT = 'vendor_products_insert';
    const TARGET_VENDOR_PRODUCTS_CONTENT = 'vendor_products_content';
    const TARGET_VENDOR_PRODUCTS_DIMENSION = 'vendor_products_dimension';
    const TARGET_VENDOR_PRICES_IMPORT = 'vendor_prices_import';
    const TARGET_VENDOR_PRICES_INSERT = 'vendor_prices_insert';

    // Picker: Orders & assembly workflow
    const TARGET_PICKER_LIST = 'picker_list';
    const TARGET_PICKER_DETAIL = 'picker_detail';
    const TARGET_PICKER_ASSEMBLY = 'picker_assembly';
    const TARGET_PICKER_ASSEMBLY_NOTIFY = 'picker_assembly_notify';
    const TARGET_PICKER_ASSEMBLY_REMOVE = 'picker_assembly_remove';
    const TARGET_PICKER_ASSEMBLY_COMPLETE = 'picker_assembly_complete';

    // POS: In-store service flow
    const TARGET_POS_LIST = 'pos_list';
    const TARGET_POS_DETAIL = 'pos_detail';
    const TARGET_POS_CLIENT_REQUEST_CODE = 'pos_client_request_code';
    const TARGET_POS_CLIENT_VERIFY_CODE = 'pos_client_verify_code';
    const TARGET_POS_COURIER_VERIFY_CODE = 'pos_courier_verify_code';
    const TARGET_POS_COMPLETE = 'pos_complete';
    const TARGET_POS_RETURN = 'pos_return';
    const TARGET_POS_ZNP = 'pos.znp';

    // Kaspi
    const TARGET_KASPI_ASSEMBLED = 'kaspi_assembled';
    const TARGET_KASPI_CODE_REQUEST = 'kaspi_code_request';
    const TARGET_KASPI_CODE_SEND = 'kaspi_code_send';
    const TARGET_KASPI_IMPORT_ORDER = 'kaspi_import_order';
    const TARGET_KASPI_IMPORT_REVIEW = 'kaspi_import_review';
    const TARGET_KASPI_SYNC_STATUS = 'kaspi_sync_status';
    const TARGET_KASPI_ORDER_WAYBILL = 'kaspi_order_waybill';

    // Jusan
    const TARGET_JUSAN_ASSEMBLED = 'jusan_assembled';
    const TARGET_JUSAN_CODE_REQUEST = 'JUSAN_CODE_REQUEST';
    const TARGET_JUSAN_CODE_SEND = 'JUSAN_CODE_SEND';
    const TARGET_JUSAN_IMPORT_ORDER = 'jusan_import_order';
    const TARGET_JUSAN_SYNC_STATUS = 'jusan_sync_status';

    // Forte
    const TARGET_FORTE_SET_PICKUP = 'forte_set_pickup';
    const TARGET_FORTE_COURIER_DELIVERY = 'forte_courier_delivery';
    const TARGET_FORTE_PENDING_PICKUP = 'forte_pending_pickup';
    const TARGET_FORTE_IMPORT_ORDER = 'forte_import_order';
    const TARGET_FORTE_SYNC_STATUS = 'forte_sync_status';
    const TARGET_FORTE_CODE_REQUEST = 'forte_code_request';
    const TARGET_FORTE_CODE_SEND = 'forte_code_send';

    // Delivery: Courier and shipping
    const TARGET_DELIVERY_CREATE = 'delivery_create';
    const TARGET_DELIVERY_CANCEL = 'delivery_cancel';
    const TARGET_DELIVERY_DELIVERED = 'delivery_delivered';
    const TARGET_DELIVERY_COURIER = 'delivery_courier';
    const TARGET_DELIVERY_ISSUED = 'delivery_issued';
    const TARGET_DELIVERY_COST = 'delivery_cost';

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

    // Stock: Inventory management
    const TARGET_STOCK_LIST = 'stock_list';
    const TARGET_STOCK_IMPORT = 'stock_import';
    const TARGET_STOCK_ITEMS_ON_HAND = 'stock_items_on_hand';
    const TARGET_STOCK_AVAILABLE_STORE = 'stock_available_store';
    const TARGET_STOCK_AVAILABLE_STORES = 'stock_available_stores';
    const TARGET_STOCK_ZNP_CREATE = 'stock_znp_create';

    // Jivosite: Chat webhook
    const TARGET_JIVOSITE_WEBHOOK = 'jivosite_webhook';

    // Zvonobot: Outbound calls
    const TARGET_ZVONOBOT_CALL = 'zvonobot_call';

    // Telegram: Bot interactions
    const TARGET_TELEGRAM_SEND = 'telegram_send';
    const TARGET_TELEGRAM_CALLBACK = 'telegram_callback';
    const TARGET_TELEGRAM_INVITE = 'telegram_invite';
    const TARGET_TELEGRAM_ORDER_TRANSFER = 'telegram_order_transfer';
    const TARGET_TELEGRAM_ORDER_KASPI_DELIVERED = 'telegram_order_kaspi_delivered';

    // Module Wolt
    const TARGET_WOLT_WEBHOOK = 'wolt_webhook';
    const TARGET_WOLT_WEBHOOK_CREATE = 'wolt_webhook_create';
    const TARGET_WOLT_WEBHOOK_READY = 'wolt_webhook_ready';
    const TARGET_WOLT_WEBHOOK_DELIVERED = 'wolt_webhook_delivered';
    const TARGET_WOLT_WEBHOOK_CANCELED = 'wolt_webhook_canceled';
    const TARGET_WOLT_GET_ORDER = 'wolt_get_order';
    const TARGET_WOLT_REPLACE = 'wolt_replace';
    const TARGET_WOLT_READY = 'wolt_ready';
    const TARGET_WOLT_ACCEPT = 'wolt_accept';
    const TARGET_WOLT_ACCEPT_PREORDER = 'wolt_accept_preorder';
    const TARGET_WOLT_CANCEL = 'wolt_cancel';

    // Module Glovo
    const TARGET_GLOVO_ORDER_CREATE = 'glovo_order_create';
    const TARGET_GLOVO_ORDER_CANCELLED = 'glovo_order_cancelled';
    const TARGET_GLOVO_ORDER_UPDATE = 'glovo_order_update';
    const TARGET_GLOVO_ORDER_READY = 'glovo_order_ready';

    // Module Yandex Eda
    const TARGET_YE_ORDER_CREATE = 'ye_order_create';
    const TARGET_YE_ORDER_STATUS = 'ye_order_status';
    const TARGET_YE_ORDER_UPDATE = 'ye_order_update';
    const TARGET_YE_ORDER_CANCELLED = 'ye_order_cancelled';
    const TARGET_YE_MENU = 'ye_menu';
    const TARGET_YE_PRICES = 'ye_prices';
    const TARGET_YE_STOCKS = 'ye_stocks';
    const TARGET_YE_MENU_EXPORT = 'ye_menu_export';

    // Wms
    const TARGET_WMS_REPORT_PICKED = 'wms_report_picked';
    const TARGET_WMS_REPORT_READY_FOR_ASSEMBLY = 'wms_report_ready_for_assembly';

    // Site
    const TARGET_SITE_ORDER_CREATE = 'site_order_create';
    const TARGET_SITE_ORDER_CANCEL = 'site_order_cancel';
    const TARGET_SITE_ORDER_CANCELED = 'site_order_canceled';
    const TARGET_SITE_ORDER_COMPLETE = 'site_order_complete';
    const TARGET_SITE_ORDER_PAYMENT = 'site_order_payment';
    const TARGET_SITE_ORDER_STOCK_LOCK = 'site_order_stock_lock';
    const TARGET_SITE_CERTIFICATE_CREATE = 'site_certificate_create';


    // Halyk
    const TARGET_HALYK_ORDER_GET = 'halyk_order_get';
    const TARGET_HALYK_ORDER_CREATE = 'halyk_order_create';
    const TARGET_HALYK_CODE_REQUEST = 'halyk_code_request';
    const TARGET_HALYK_CODE_SEND = 'halyk_code_send';
    const TARGET_HALYK_ACCEPT_EXTERNAL = 'halyk_accept_external';
    const TARGET_HALYK_STATUS_SYNC = 'halyk_status_sync';
    const TARGET_HALYK_PAYMENT_SUCCESS = 'halyk_payment_success';
    const TARGET_HALYK_PAYMENT_FAILURE = 'halyk_payment_failure';

    // Two gis
    const TARGET_TWO_GIS_IMPORT_REVIEW = 'two_gis_import_review';

    // Yandex
    const TARGET_YANDEX_IMPORT_REVIEW = 'yandex_import_review';

    // Google
    const TARGET_GOOGLE_IMPORT_REVIEW = 'google_import_review';

    // Ozon
    const TARGET_OZON_IMPORT = 'ozon_import';
    const TARGET_OZON_ORDER_GET = 'ozon_order_get';
    const TARGET_OZON_ORDER_CREATE = 'ozon_order_create';
    const TARGET_OZON_ORDER_SHIP = 'ozon_order_ship';
    const TARGET_OZON_ORDER_CANCEL = 'ozon_order_cancel';
    const TARGET_OZON_ORDER_WAYBILL = 'ozon_order_waybill';
    const TARGET_OZON_ORDER_BARCODES = 'ozon_order_barcodes';
    const TARGET_OZON_STATUS_SYNC = 'ozon_status_sync';
    const TARGET_OZON_PRODUCTS = 'ozon_products';
    const TARGET_OZON_STOCKS = 'ozon_stocks';

    // WB
    const TARGET_WB_IMPORT_ORDER = 'wb_import_order';
    const TARGET_WB_IMPORT_REVIEW = 'wb_import_review';
    const TARGET_WB_SYNC_STATUS = 'wb_sync_status';
    const TARGET_WB_TRANSIT = 'wb_transit';
    const TARGET_WB_STOCKS = 'wb_stocks';

    // Module Sl
    const TARGET_SL_COMPLETE = 'sl_complete';
    const TARGET_SL_CANCEL = 'sl_cancel';
    const TARGET_SL_CUSTOMER_HANDLE = 'sl_customer_handle';
    const TARGET_SL_CUSTOMER_DETAIL = 'sl_customer_detail';
    const TARGET_SL_CUSTOMER_CREATE = 'sl_customer_create';
    const TARGET_SL_WITHDRAW = 'sl_withdraw';
    const TARGET_SL_REPLACE = 'sl_replace';

    // Module RetailHub
    const TARGET_RETAILHUB_NOTIFY = 'retailhub_notify';
    const TARGET_RETAILHUB_ORDER_PREPARE_COMPLETE = 'retailhub_order_prepare_complete';
    const TARGET_RETAILHUB_ORDER_COMPLETE = 'retailhub_order_complete';
    const TARGET_RETAILHUB_ORDER_RECIPE = 'retailhub_order_recipe';
    const TARGET_RETAILHUB_ORDER_COMPLETE_PROBLEM = 'retailhub_order_complete_problem';

    // Module Mail
    const TARGET_MAIL_SEND_MESSAGE = 'mail_send_message';

    // Module Devino
    const TARGET_DEVINO_SEND_MESSAGE = 'devino_send_message';

    // Module Infobip
    const TARGET_INFOBIP_SEND_MESSAGE = 'infobip_send_message';

    // Module Edna
    const TARGET_EDNA_SEND_MESSAGE = 'edna_send_message';

    // App
    const TARGET_MOBILE_ORDER_CREATE = 'mobile_order_create';
    const TARGET_MOBILE_ORDER_CANCELED = 'mobile_order_canceled';
    const TARGET_MOBILE_ORDER_COMPLETE = 'mobile_order_complete';
    const TARGET_MOBILE_ORDER_PAYMENT = 'mobile_order_payment';

    // Module Ax
    const TARGET_AX_ZNP_STATUS = 'ax_znp_status';
    const TARGET_AX_EPS_BALANCE = 'ax_eps_balance';
    const TARGET_AX_EPS_PAYMENT = 'ax_eps_payment';

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

            // Vendor
            self::TARGET_VENDOR_STOCKS_LOAD => 'VENDOR_STOCKS_LOAD',
            self::TARGET_VENDOR_PRODUCTS_IMPORT => 'VENDOR_PRODUCTS_IMPORT',
            self::TARGET_VENDOR_PRODUCTS_INSERT => 'VENDOR_PRODUCTS_INSERT',
            self::TARGET_VENDOR_PRODUCTS_CONTENT => 'VENDOR_PRODUCTS_CONTENT',
            self::TARGET_VENDOR_PRICES_IMPORT => 'VENDOR_PRICES_IMPORT',
            self::TARGET_VENDOR_PRICES_INSERT => 'VENDOR_PRICES_INSERT',
            self::TARGET_VENDOR_PRODUCTS_DIMENSION => 'VENDOR_PRODUCTS_DIMENSION',

            // Picker
            self::TARGET_PICKER_LIST => 'PICKER_LIST',
            self::TARGET_PICKER_DETAIL => 'PICKER_DETAIL',
            self::TARGET_PICKER_ASSEMBLY => 'PICKER_ASSEMBLY',
            self::TARGET_PICKER_ASSEMBLY_NOTIFY => 'PICKER_ASSEMBLY_NOTIFY',
            self::TARGET_PICKER_ASSEMBLY_COMPLETE => 'PICKER_ASSEMBLY_COMPLETE',
            self::TARGET_PICKER_ASSEMBLY_REMOVE => 'PICKER_ASSEMBLY_REMOVE',

            // POS
            self::TARGET_POS_LIST => 'POS_LIST',
            self::TARGET_POS_DETAIL => 'POS_DETAIL',
            self::TARGET_POS_CLIENT_REQUEST_CODE => 'POS_CLIENT_REQUEST_CODE',
            self::TARGET_POS_CLIENT_VERIFY_CODE => 'POS_CLIENT_VERIFY_CODE',
            self::TARGET_POS_COURIER_VERIFY_CODE => 'POS_COURIER_VERIFY_CODE',
            self::TARGET_POS_COMPLETE => 'POS_COMPLETE',
            self::TARGET_POS_RETURN => 'POS_RETURN',
            self::TARGET_POS_ZNP => 'POS_ZNP',

            // Kaspi
            self::TARGET_KASPI_ASSEMBLED => 'KASPI_ASSEMBLED',
            self::TARGET_KASPI_CODE_REQUEST => 'KASPI_CODE_REQUEST',
            self::TARGET_KASPI_CODE_SEND => 'KASPI_CODE_SEND',
            self::TARGET_KASPI_IMPORT_ORDER => 'KASPI_IMPORT_ORDER',
            self::TARGET_KASPI_IMPORT_REVIEW => 'KASPI_IMPORT_REVIEW',
            self::TARGET_KASPI_SYNC_STATUS => 'KASPI_SYNC_STATUS',
            self::TARGET_KASPI_ORDER_WAYBILL => 'KASPI_ORDER_WAYBILL',

            // Jusan
            self::TARGET_JUSAN_ASSEMBLED => 'JUSAN_ASSEMBLED',
            self::TARGET_JUSAN_CODE_REQUEST => 'JUSAN_CODE_REQUEST',
            self::TARGET_JUSAN_CODE_SEND => 'JUSAN_CODE_SEND',
            self::TARGET_JUSAN_IMPORT_ORDER => 'JUSAN_IMPORT_ORDER',
            self::TARGET_JUSAN_SYNC_STATUS => 'JUSAN_SYNC_STATUS',

            // Forte
            self::TARGET_FORTE_SET_PICKUP => 'FORTE_SET_PICKUP',
            self::TARGET_FORTE_COURIER_DELIVERY => 'FORTE_COURIER_DELIVERY',
            self::TARGET_FORTE_PENDING_PICKUP => 'FORTE_PENDING_PICKUP',
            self::TARGET_FORTE_IMPORT_ORDER => 'FORTE_IMPORT_ORDER',
            self::TARGET_FORTE_SYNC_STATUS => 'FORTE_SYNC_STATUS',
            self::TARGET_FORTE_CODE_REQUEST => 'FORTE_CODE_REQUEST',
            self::TARGET_FORTE_CODE_SEND => 'FORTE_CODE_SEND',

            // Delivery
            self::TARGET_DELIVERY_CREATE => 'DELIVERY_CREATE',
            self::TARGET_DELIVERY_CANCEL => 'DELIVERY_CANCEL',
            self::TARGET_DELIVERY_DELIVERED => 'DELIVERY_DELIVERED',
            self::TARGET_DELIVERY_COURIER => 'DELIVERY_COURIER',
            self::TARGET_DELIVERY_ISSUED => 'DELIVERY_ISSUED',
            self::TARGET_DELIVERY_COST => 'DELIVERY_COST',

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

            // Stock
            self::TARGET_STOCK_LIST => 'STOCK_LIST',
            self::TARGET_STOCK_IMPORT => 'STOCK_IMPORT',
            self::TARGET_STOCK_ITEMS_ON_HAND => 'STOCK_ITEMS_ON_HAND',
            self::TARGET_STOCK_AVAILABLE_STORE => 'STOCK_AVAILABLE_STORE',
            self::TARGET_STOCK_AVAILABLE_STORES => 'STOCK_AVAILABLE_STORES',
            self::TARGET_STOCK_ZNP_CREATE => 'STOCK_ZNP_CREATE',

            // Jivosite
            self::TARGET_JIVOSITE_WEBHOOK => 'JIVOSITE_WEBHOOK',

            // Zvonobot
            self::TARGET_ZVONOBOT_CALL => 'ZVONOBOT_CALL',

            // Telegram
            self::TARGET_TELEGRAM_SEND => 'TELEGRAM_SEND',
            self::TARGET_TELEGRAM_CALLBACK => 'TELEGRAM_CALLBACK',
            self::TARGET_TELEGRAM_INVITE => 'TELEGRAM_INVITE',
            self::TARGET_TELEGRAM_ORDER_TRANSFER => 'TELEGRAM_ORDER_TRANSFER',
            self::TARGET_TELEGRAM_ORDER_KASPI_DELIVERED => 'TELEGRAM_ORDER_KASPI_DELIVERED',

            // Module Wolt
            self::TARGET_WOLT_WEBHOOK => 'WOLT_WEBHOOK',
            self::TARGET_WOLT_WEBHOOK_CREATE => 'WOLT_WEBHOOK_CREATE',
            self::TARGET_WOLT_WEBHOOK_READY => 'WOLT_WEBHOOK_READY',
            self::TARGET_WOLT_WEBHOOK_DELIVERED => 'WOLT_WEBHOOK_DELIVERED',
            self::TARGET_WOLT_WEBHOOK_CANCELED => 'WOLT_WEBHOOK_CANCELED',
            self::TARGET_WOLT_GET_ORDER => 'WOLT_GET_ORDER',
            self::TARGET_WOLT_REPLACE => 'WOLT_REPLACE',
            self::TARGET_WOLT_READY => 'WOLT_READY',
            self::TARGET_WOLT_ACCEPT => 'WOLT_ACCEPT',
            self::TARGET_WOLT_ACCEPT_PREORDER => 'WOLT_ACCEPT_PREORDER',
            self::TARGET_WOLT_CANCEL => 'WOLT_CANCEL',

            // Glovo
            self::TARGET_GLOVO_ORDER_CREATE => 'GLOVO_ORDER_CREATE',
            self::TARGET_GLOVO_ORDER_CANCELLED => 'GLOVO_ORDER_CANCELLED',
            self::TARGET_GLOVO_ORDER_UPDATE => 'GLOVO_ORDER_UPDATE',
            self::TARGET_GLOVO_ORDER_READY => 'GLOVO_ORDER_READY',

            // Ye
            self::TARGET_YE_ORDER_CREATE => 'YE_ORDER_CREATE',
            self::TARGET_YE_ORDER_STATUS => 'YE_ORDER_STATUS',
            self::TARGET_YE_ORDER_UPDATE => 'YE_ORDER_UPDATE',
            self::TARGET_YE_ORDER_CANCELLED => 'YE_ORDER_CANCELLED',
            self::TARGET_YE_MENU => 'YE_MENU',
            self::TARGET_YE_PRICES => 'YE_PRICES',
            self::TARGET_YE_STOCKS => 'YE_STOCKS',
            self::TARGET_YE_MENU_EXPORT => 'YE_MENU_EXPORT',

            // Wms
            self::TARGET_WMS_REPORT_PICKED => 'WMS_REPORT_PICKED',
            self::TARGET_WMS_REPORT_READY_FOR_ASSEMBLY => 'WMS_REPORT_READY_FOR_ASSEMBLY',

            // Site
            self::TARGET_SITE_ORDER_CREATE => 'SITE_ORDER_CREATE',
            self::TARGET_SITE_ORDER_CANCEL => 'SITE_ORDER_CANCEL',
            self::TARGET_SITE_ORDER_CANCELED => 'SITE_ORDER_CANCELED',
            self::TARGET_SITE_ORDER_COMPLETE => 'SITE_ORDER_COMPLETE',
            self::TARGET_SITE_ORDER_PAYMENT => 'SITE_ORDER_PAYMENT',
            self::TARGET_SITE_ORDER_STOCK_LOCK => 'SITE_ORDER_STOCK_LOCK',
            self::TARGET_SITE_CERTIFICATE_CREATE => 'SITE_CERTIFICATE_CREATE',

            // Halyk
            self::TARGET_HALYK_ORDER_CREATE => 'HALYK_ORDER_CREATE',
            self::TARGET_HALYK_CODE_REQUEST => 'HALYK_CODE_REQUEST',
            self::TARGET_HALYK_CODE_SEND => 'HALYK_CODE_SEND',
            self::TARGET_HALYK_ACCEPT_EXTERNAL => 'HALYK_ACCEPT_EXTERNAL',
            self::TARGET_HALYK_ORDER_GET => 'HALYK_ORDER_GET',
            self::TARGET_HALYK_STATUS_SYNC => 'HALYK_STATUS_SYNC',
            self::TARGET_HALYK_PAYMENT_SUCCESS => 'HALYK_PAYMENT_SUCCESS',
            self::TARGET_HALYK_PAYMENT_FAILURE => 'HALYK_PAYMENT_FAILURE',

            // Two gis
            self::TARGET_TWO_GIS_IMPORT_REVIEW => 'TWO_GIS_IMPORT_REVIEW',

            // Yandex
            self::TARGET_YANDEX_IMPORT_REVIEW => 'YANDEX_IMPORT_REVIEW',

            // Google
            self::TARGET_GOOGLE_IMPORT_REVIEW => 'GOOGLE_IMPORT_REVIEW',

            // Ozon
            self::TARGET_OZON_IMPORT => 'OZON_IMPORT',
            self::TARGET_OZON_ORDER_GET => 'OZON_ORDER_GET',
            self::TARGET_OZON_ORDER_CREATE => 'OZON_ORDER_CREATE',
            self::TARGET_OZON_ORDER_SHIP => 'OZON_ORDER_SHIP',
            self::TARGET_OZON_ORDER_CANCEL => 'OZON_ORDER_CANCEL',
            self::TARGET_OZON_ORDER_WAYBILL => 'OZON_ORDER_WAYBILL',
            self::TARGET_OZON_ORDER_BARCODES => 'OZON_ORDER_BARCODES',
            self::TARGET_OZON_STATUS_SYNC => 'OZON_STATUS_SYNC',
            self::TARGET_OZON_PRODUCTS => 'OZON_PRODUCTS',
            self::TARGET_OZON_STOCKS => 'OZON_STOCKS',

            // WB
            self::TARGET_WB_IMPORT_ORDER => 'WB_IMPORT_ORDER',
            self::TARGET_WB_IMPORT_REVIEW => 'WB_IMPORT_REVIEW',
            self::TARGET_WB_SYNC_STATUS => 'WB_SYNC_STATUS',
            self::TARGET_WB_TRANSIT => 'WB_TRANSIT',
            self::TARGET_WB_STOCKS => 'WB_STOCKS',

            // WB
            self::TARGET_SL_COMPLETE => 'SL_COMPLETE',
            self::TARGET_SL_CANCEL => 'SL_CANCEL',
            self::TARGET_SL_CUSTOMER_HANDLE => 'SL_CUSTOMER_HANDLE',
            self::TARGET_SL_CUSTOMER_DETAIL => 'SL_CUSTOMER_DETAIL',
            self::TARGET_SL_CUSTOMER_CREATE => 'SL_CUSTOMER_CREATE',
            self::TARGET_SL_WITHDRAW => 'SL_WITHDRAW',
            self::TARGET_SL_REPLACE => 'SL_REPLACE',

            // Retail hub
            self::TARGET_RETAILHUB_NOTIFY => 'RETAILHUB_NOTIFY',
            self::TARGET_RETAILHUB_ORDER_PREPARE_COMPLETE => 'RETAILHUB_ORDER_PREPARE_COMPLETE',
            self::TARGET_RETAILHUB_ORDER_COMPLETE => 'RETAILHUB_ORDER_COMPLETE',
            self::TARGET_RETAILHUB_ORDER_RECIPE => 'RETAILHUB_ORDER_RECIPE',
            self::TARGET_RETAILHUB_ORDER_COMPLETE_PROBLEM => 'RETAILHUB_ORDER_COMPLETE_PROBLEM',

            // Mail
            self::TARGET_MAIL_SEND_MESSAGE => 'MAIL_SEND_MESSAGE',

            // Devino
            self::TARGET_DEVINO_SEND_MESSAGE => 'DEVINO_SEND_MESSAGE',

            // Infobip
            self::TARGET_INFOBIP_SEND_MESSAGE => 'INFOBIP_SEND_MESSAGE',

            // Edna
            self::TARGET_EDNA_SEND_MESSAGE => 'EDNA_SEND_MESSAGE',

            // Mobile
            self::TARGET_MOBILE_ORDER_CREATE => 'MOBILE_ORDER_CREATE',
            self::TARGET_MOBILE_ORDER_CANCELED => 'MOBILE_ORDER_CANCELED',
            self::TARGET_MOBILE_ORDER_COMPLETE => 'MOBILE_ORDER_COMPLETE',
            self::TARGET_MOBILE_ORDER_PAYMENT => 'MOBILE_ORDER_PAYMENT',

            self::TARGET_AX_ZNP_STATUS => 'AX_ZNP_STATUS',
            self::TARGET_AX_EPS_BALANCE => 'AX_EPS_BALANCE',
            self::TARGET_AX_EPS_PAYMENT => 'AX_EPS_PAYMENT',
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