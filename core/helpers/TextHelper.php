<?php

namespace app\core\helpers;

use Exception;
use yii\helpers\Html;
use app\entities\Order;
use app\entities\LeadEvent;
use app\entities\CareEvent;
use app\entities\OrderEvent;
use yii\helpers\ArrayHelper;

/**
 * Text helper
 */
class TextHelper
{
    /**
     * @param $name
     * @param $minutes
     * @return string
     */
    public static function orderAssemblyNotify($name, $minutes): string
    {
        return "Точка $name. Уведомления время ожидания сборки превысило: $minutes минут";
    }

    /**
     * @param $number
     * @return mixed
     */
    public static function getShortNumber($number): mixed
    {
        if (!is_numeric($number)){
            return $number;
        }
        if ($number <= 999){
            return $number;
        }
        if ($number <= 999999){
            return round($number / 1000) . 'K';
        }
        if ($number <= 999999999){
            return round($number / 1000000) . 'M';
        }
        if ($number <= 999999999999){
            return round($number / 1000000000) . 'B';
        }

        return $number;
    }

    /**
     * @param $cost
     * @return string
     */
    public static function orderPaid($cost): string
    {
        return "Оплата заказа на сумму: $cost";
    }

    /**
     * @param $id
     * @param $number
     * @return string
     */
    public static function getTelegramOrderTransfer($id, $number): string
    {
        $link = 'https://servicemarwin.com/order?id=' . $id;
        return '🔥' . "<b>Вам передан заказ #{$number}</b>" . "\n\n" . $link;
    }

    /**
     * @param $number
     * @param $vendorNumber
     * @param $storeNumber
     * @return string
     */
    public static function getTelegramKaspiDelivered($number, $vendorNumber, $storeNumber): string
    {
        return '🔥' . "<b>Kaspi заказ ВЫДАН</b>" . "\n\nНомер: $number" . "\nНомер канала: $vendorNumber" . "\nСклад: $storeNumber";
    }

    /**
     * @param $name
     * @return string
     */
    public static function transferOrder($name): string
    {
        return "Заказ передан: $name";
    }

    /**
     * @param OrderEvent $event
     * @return string
     * @throws Exception
     */
    public static function getOrderMessage(OrderEvent $event): string
    {
        if ($event->type == OrderEventHelper::TYPE_ASSEMBLY_ERROR) {
            return self::assemblyChanges($event);
        }

        if ($event->type == OrderEventHelper::TYPE_ASSEMBLY_CREATED) {
            $storeName = ArrayHelper::getValue($event->data, 'name');
            $storeId = ArrayHelper::getValue($event->data, 'store_id');
            $type = ArrayHelper::getValue($event->data, 'type');
            $link = Html::a($storeName, ['store/view', 'id' => $storeId], ['data-pjax' => 0, 'class' => 'js-view-modal']);

            $result = "$link. Сборка создана";

            if ($type == OrderStoreHelper::TYPE_MOVE){
                $result .= "<span class='order-chat__item_detail'>Тип: Перемещение</span>";
            }

            return $result;
        }

        if ($event->type == OrderEventHelper::TYPE_ASSEMBLY_CONFIRMED) {
            $storeName = ArrayHelper::getValue($event->data, 'name');
            $storeId = ArrayHelper::getValue($event->data, 'store_id');
            $link = Html::a($storeName, ['store/view', 'id' => $storeId], ['data-pjax' => 0, 'class' => 'js-view-modal']);

            return "$link. Сборка подтверждена";
        }

        if ($event->type == OrderEventHelper::TYPE_ASSEMBLY_REMOVED) {
            $storeName = ArrayHelper::getValue($event->data, 'name');
            $storeId = ArrayHelper::getValue($event->data, 'store_id');
            $link = Html::a($storeName, ['store/view', 'id' => $storeId], ['data-pjax' => 0, 'class' => 'js-view-modal']);

            return "$link. Сборка удалена";
        }

        if ($event->type == OrderEventHelper::TYPE_ASSEMBLY_PARTIAL) {
            $storeName = ArrayHelper::getValue($event->data, 'name');
            $storeId = ArrayHelper::getValue($event->data, 'store_id');
            $products = ArrayHelper::getValue($event->data, 'products');

            $reasonText = '';

            foreach ($products as $product) {
                $reasonText .= '<span class="order-chat__assembly_item">' . $product['sku'] . ' - кол-во: ' . floor($product['quantity']) . ' остаток: ' . (float)$product['quantity_available'] . '</span>';
            }


            $link = Html::a($storeName, ['store/view', 'id' => $storeId], ['data-pjax' => 0, 'class' => 'js-view-modal']);

            return $link . ' Сборка не подтверждена: <span class="order-chat__assembly">' . $reasonText . '</span>';
        }

        if ($event->type == OrderEventHelper::TYPE_POS_COMPLETE) {
            $storeName = ArrayHelper::getValue($event->data, 'name');
            $link = 'чек';

            if ($receiptId = ArrayHelper::getValue($event->data, 'receipt_id')) {
                $link = Html::a($link, ['order/receipt', 'id' => $receiptId], ['data-pjax' => 0, 'class' => 'js-view-modal']);
            }

            return "$storeName. Пробит $link продажи";
        }

        if ($event->type == OrderEventHelper::TYPE_POS_RETURN) {
            $storeName = ArrayHelper::getValue($event->data, 'name');
            $link = 'чек';

            if ($receiptId = ArrayHelper::getValue($event->data, 'receipt_id')) {
                $link = Html::a($link, ['order/receipt', 'id' => $receiptId], ['data-pjax' => 0, 'class' => 'js-view-modal']);
            }

            return "$storeName. Пробит $link возврата";
        }

        if ($event->type == OrderEventHelper::TYPE_ZNP_CREATED) {
            $storeName = ArrayHelper::getValue($event->data, 'name');
            $virtualStoreNumber = ArrayHelper::getValue($event->data, 'virtual_store_number');
            $storeId = ArrayHelper::getValue($event->data, 'store_id');
            $link = Html::a($storeName, ['store/view', 'id' => $storeId], ['data-pjax' => 0, 'class' => 'js-view-modal']);
            $externalNumber = ArrayHelper::getValue($event->data, 'external_number', $event->order->external_number);

            $result = "$link. ЗНП создан";
            $body =  'Номер: ' . ArrayHelper::getValue($event->data, 'transfer_id');

            if ($virtualStoreNumber) {
                $body .= Html::tag('br') . 'Виртуальный склад: ' . $virtualStoreNumber;
            }

            if ($externalNumber){
                $body .= Html::tag('br') . 'Внешний номер: ' . $externalNumber;
            }

            return $result . "<span class='order-chat__item_detail'>" . $body . "</span>";
        }

        if ($event->type == OrderEventHelper::TYPE_ZNP_RECEIVED) {
            $storeName = ArrayHelper::getValue($event->data, 'name');
            $virtualStoreNumber = ArrayHelper::getValue($event->data, 'virtual_store_number');
            $storeId = ArrayHelper::getValue($event->data, 'store_id');
            $link = Html::a($storeName, ['store/view', 'id' => $storeId], ['data-pjax' => 0, 'class' => 'js-view-modal']);

            $result = "$link. ЗНП разнесен";
            $body =  'Номер: ' . ArrayHelper::getValue($event->data, 'transfer_id');

            if ($virtualStoreNumber) {
                $body .= Html::tag('br') . 'Виртуальный склад: ' . $virtualStoreNumber;
            }

            return $result . "<span class='order-chat__item_detail'>" . $body . "</span>";
        }

        if ($event->type == OrderEventHelper::TYPE_ZNP_CREATED_ERROR) {
            $storeName = ArrayHelper::getValue($event->data, 'name');
            $virtualStoreNumber = ArrayHelper::getValue($event->data, 'virtual_store_number');
            $storeId = ArrayHelper::getValue($event->data, 'store_id');
            $link = Html::a($storeName, ['store/view', 'id' => $storeId], ['data-pjax' => 0, 'class' => 'js-view-modal']);
            $externalNumber = ArrayHelper::getValue($event->data, 'external_number', $event->order->external_number);

            $result = "$link. Ошибка при создании ЗНП";

            if ($virtualStoreNumber) {
                $result .= Html::tag('br') . 'Виртуальный склад: ' . $virtualStoreNumber;
            }

            if ($externalNumber){
                $result .= Html::tag('br') . 'Внешний номер: ' . $externalNumber;
            }

            if ($error = ArrayHelper::getValue($event->data, 'error')){
                $result .= Html::tag('br') . Html::encode($error);
            }

            return $result;
        }

        // Kaspi
        if ($event->type == OrderEventHelper::TYPE_KASPI_CANCELLED) {
            return 'Заказ отменен в Kaspi Shop';
        }
        if ($event->type == OrderEventHelper::TYPE_KASPI_COMPLETED) {
            return 'Заказ успешно завершён в Kaspi Shop';
        }
        if ($event->type == OrderEventHelper::TYPE_KASPI_CHANGE_QUANTITY) {
            $productName = ArrayHelper::getValue($event->data, 'name');
            $result = 'Количество товаров в Kaspi Shop успешно изменено';

            return $result . "<span class='order-chat__item_detail'>" . $productName . "</span>";
        }
        if ($event->type == OrderEventHelper::TYPE_KASPI_CHANGE_QUANTITY_ERROR) {
            $productName = ArrayHelper::getValue($event->data, 'name');
            $result = 'При изменении количество товаров в Kaspi Shop произошла ошибка';

            return $result . "<span class='order-chat__item_detail'>" . $productName . "</span>";
        }
        if ($event->type == OrderEventHelper::TYPE_KASPI_SAVE_WAYBILL) {
            $link = ArrayHelper::getValue($event->data, 'link');
            $link = Html::a('Накладная', $link, ['target' => '_blank', 'data-pjax' => 0]);

            return "$link успешно сохранена";
        }
        if ($event->type == OrderEventHelper::TYPE_KASPI_SAVE_WAYBILL_ERROR) {
            return "При скачивании накладной произошла ошибка";
        }

        if ($event->type == OrderEventHelper::TYPE_PENDING) {
            return "Заказ отложен по причине: " . ArrayHelper::getValue($event->data, 'reason');
        }

        if ($event->type == OrderEventHelper::TYPE_SIGNATURE_REQUIRED) {
            return 'Необходимо подписания кредита';
        }

        // Jusan
        if ($event->type == OrderEventHelper::TYPE_JUSAN_CANCELLED) {
            return 'Заказ отменен в Jusan';
        }
        if ($event->type == OrderEventHelper::TYPE_JUSAN_COMPLETED) {
            return 'Заказ успешно завершён в Jusan';
        }
        if ($event->type == OrderEventHelper::TYPE_JUSAN_CHANGE_QUANTITY) {
            $productName = ArrayHelper::getValue($event->data, 'name');
            $result = 'Количество товаров в Jusan успешно изменено';

            return $result . "<span class='order-chat__item_detail'>" . $productName . "</span>";
        }
        if ($event->type == OrderEventHelper::TYPE_JUSAN_CHANGE_QUANTITY_ERROR) {
            $productName = ArrayHelper::getValue($event->data, 'name');
            $result = 'При изменении количество товаров в Jusan произошла ошибка';

            return $result . "<span class='order-chat__item_detail'>" . $productName . "</span>";
        }

        // Halyk
        if ($event->type == OrderEventHelper::TYPE_HALYK_COMPLETED) {
            return 'Заказ успешно завершён в Halyk Market';
        }

        // Ozon
        if ($event->type == OrderEventHelper::TYPE_OZON_WAYBILL) {
            $link = ArrayHelper::getValue($event->data, 'link');
            $link = Html::a('Накладная', $link, ['target' => '_blank', 'data-pjax' => 0]);

            return "$link успешно сохранена";
        }

        // WB
        if ($event->type == OrderEventHelper::TYPE_WB_CANCELLED) {
            return 'Заказ отменен в Wildberries';
        }
        if ($event->type == OrderEventHelper::TYPE_WB_COMPLETED) {
            return 'Заказ успешно завершён в Wildberries';
        }
        if ($event->type == OrderEventHelper::TYPE_WB_SAVE_WAYBILL) {
            $link = ArrayHelper::getValue($event->data, 'link');
            $link = Html::a('Накладная', $link, ['target' => '_blank', 'data-pjax' => 0]);

            $result = "$link успешно сохранена";
            $supplyId = ArrayHelper::getValue($event->order->extra_fields, 'wb_supply_id');
            if ($supplyId){
                $result .= Html::tag('br') . 'Поставка: ' . $supplyId;
            }

            return $result;
        }
        if ($event->type == OrderEventHelper::TYPE_WB_SAVE_WAYBILL_ERROR) {
            return "При скачивании накладной произошла ошибка";
        }

        // Forte
        if ($event->type == OrderEventHelper::TYPE_FORTE_CANCELLED) {
            return 'Заказ отменен в Forte';
        }
        if ($event->type == OrderEventHelper::TYPE_FORTE_COMPLETED) {
            return 'Заказ успешно завершён в Forte';
        }

        // SL
        if ($event->type == OrderEventHelper::TYPE_SL_COMPLETE) {
            return 'Заказ завершен в SL';
        }
        if ($event->type == OrderEventHelper::TYPE_SL_COMPLETE_ERROR) {
            $result = 'Ошибка при завершении заказа в SL';
            $error = ArrayHelper::getValue($event->data, 'error');
            if ($error){
                $result .= Html::tag('br') . $error;
            }
            return $result;
        }
        if ($event->type == OrderEventHelper::TYPE_SL_CANCEL) {
            return 'Заказ отменен в SL';
        }
        if ($event->type == OrderEventHelper::TYPE_SL_CANCEL_ERROR) {
            $result = 'Ошибка при отмене заказа в SL';
            $error = ArrayHelper::getValue($event->data, 'error');
            if ($error){
                $result .= Html::tag('br') . $error;
            }
            return $result;
        }

        if ($event->type == OrderEventHelper::TYPE_TG_KASPI_DELIVERED) {
            return 'Уведомления о доставленном заказе';
        }

        if ($event->type == OrderEventHelper::TYPE_PICKER_ASSEMBLY) {
            $storeName = ArrayHelper::getValue($event->data, 'name');
            $storeId = ArrayHelper::getValue($event->data, 'store_id');
            $link = Html::a($storeName, ['store/view', 'id' => $storeId], ['data-pjax' => 0, 'class' => 'js-view-modal']);

            $result = "$link. Сборка передана в ИЗИ";
            $result .= "<span class='order-chat__item_detail'>" . 'ID: ' . ArrayHelper::getValue($event->data, 'order_store_id') . "</span>";

            return $result;
        }

        // Bonus
        if ($event->type == OrderEventHelper::TYPE_BONUS_DISTRIBUTE) {
            $calc = ArrayHelper::getValue($event->data, 'calc', []);
            $result = 'Бонусы распределены по товарам';

            if ($calc){
                $result .= Html::tag('br');
                foreach ($calc as $calcItem){
                    $sku = ArrayHelper::getValue($calcItem, 'sku', 'Undefined SKU');
                    $bonus = ArrayHelper::getValue($calcItem, 'bonus', 0);
                    $percent = ArrayHelper::getValue($calcItem, 'percent', 0);
                    $result .= Html::tag('br') . $sku . ' - ' . $bonus . ' (' . ($percent * 100) . '%)';
                }
            }

            return $result;
        }

        // Order notify
        if ($event->type == OrderEventHelper::TYPE_NOTIFY_PICKUP_READY){
            $storeName = ArrayHelper::getValue($event->data, 'store_name');
            $number = ArrayHelper::getValue($event->data, 'number');
            $providerName = NotifyHelper::getProviderName(ArrayHelper::getValue($event->data, 'provider'));
            $lang = ArrayHelper::getValue($event->data, 'lang', 'ru');

            if ($lang == 'kk'){
                return "<strong>Отправлено сообщение ($providerName):</strong><br />
                    Сәлеметсіз бе! Сіздің тапсырысыңыз $storeName дүкенінде алуға дайын. 
                    Тапсырысты алу нөмірі: $number. Бізді таңдағаңызға рахмет!";
            }

            return "<strong>Отправлено сообщение ($providerName):</strong><br />
                    Ваш заказ готов к выдаче. Номер заказа: $number. Адрес пункта выдачи: $storeName. Вы можете забрать заказ в удобное для вас время. Если возникнут вопросы, пожалуйста, свяжитесь с нами.";
        }

        // Order notify
        if ($event->type == OrderEventHelper::TYPE_NOTIFY_CANCELLED){
            $number = ArrayHelper::getValue($event->data, 'number');
            $providerName = NotifyHelper::getProviderName(ArrayHelper::getValue($event->data, 'provider'));

            return "<strong>Отправлено сообщение ($providerName):</strong><br />
                    Ваш заказ $number был отменен. Если у вас возникли вопросы или вы хотите оформить новый заказ, пожалуйста, свяжитесь с нами. Спасибо, что обратились к нам.";
        }

        if ($event->type == OrderEventHelper::TYPE_CORRECT_SUCCESS) {
            return 'Автоматическая корректировка успешно выполнена';
        }

        if ($event->type == OrderEventHelper::TYPE_ORDER_RETURN_CANCELED) {
            return 'Отмена заказа из-за полного возврата';
        }

        if ($event->type == OrderEventHelper::TYPE_MOVE_SUCCESS) {
            return 'Процесс частичного выкупа успешно запущен';
        }

        if ($event->type == OrderEventHelper::TYPE_CANCEL) {
            $result = 'Не заполнено';
            $reason = ArrayHelper::getValue($event->data, 'reason');
            $reasonAdditional = ArrayHelper::getValue($event->data, 'reason_additional');
            if ($reason){
                $result = $reason;
            }
            if ($reasonAdditional){
                $result .= ' (' . $reasonAdditional . ')';
            }
            return 'Причина отмены: ' . $result;
        }

		if ($event->type == OrderEventHelper::TYPE_EMEX_PDF) {
			$link = ArrayHelper::getValue($event->data, 'link');
			$link = Html::a('PDF для EMEX', $link, ['target' => '_blank', 'data-pjax' => 0]);

			return "$link успешно сгенерирована";
		}

        // Payment success
        if ($event->type == OrderEventHelper::TYPE_PAYMENT_SUCCESS) {
            $method = ArrayHelper::getValue($event->data, 'method');
            $amount = ArrayHelper::getValue($event->data, 'amount');
            $type = ArrayHelper::getValue($event->data, 'type');
            $balance =  ArrayHelper::getValue($event->data, 'balance');
            $transaction =  ArrayHelper::getValue($event->data, 'transaction_id');

            $header = 'Успешная оплата';

            if ($type == PaymentHelper::TYPE_RETURN){
                $header = 'Успешный возврат средств';
            }

            if ($method && $method == PaymentHelper::METHOD_BONUS) {
                $header = 'Успешное спиание';
            }

            if ($method && $method == PaymentHelper::METHOD_EPS && $transaction) {
                $transaction = PaymentHelper::maskTransactionId($transaction);
            }

            $body = '';

            if ($method) {
                $body .= "<span class='order-chat__detail'>Способ: " . PaymentHelper::getMethodName($method) . "</span>";
            }
            if ($amount) {
                $body .= "<span class='order-chat__detail'>Сумма: " . (float)$amount . " ₸</span>";
            }
            if ($transaction) {
                $body .= "<span class='order-chat__detail'>Транзакция: " . $transaction . "</span>";
            }

            if ($balance) {
                $body .= "<span class='order-chat__detail'>Баланс: " . (float)$balance . " ₸</span>";
            }

            return $header . "<div class='order-chat__details'>" . $body . '</div>';
        }

        // Payment paid
        if ($event->type == OrderEventHelper::TYPE_PAYMENT_PREPAID) {
            $method = ArrayHelper::getValue($event->data, 'method');
            $amount = ArrayHelper::getValue($event->data, 'amount');
            $transaction = ArrayHelper::getValue($event->data, 'transaction_id');
            $header = 'Заказ оплачен';

            if ($method && $method == PaymentHelper::METHOD_EPS && $transaction) {
                $transaction = PaymentHelper::maskTransactionId($transaction);
            }

            $body = '';

            if ($method) {
                $body .= "<span class='order-chat__detail'>Способ: " . PaymentHelper::getMethodName($method) . "</span>";
            }
            if ($amount) {
                $body .= "<span class='order-chat__detail'>Сумма: " . (float)$amount . " ₸</span>";
            }
            if ($transaction) {
                $body .= "<span class='order-chat__detail'>Транзакция: " . $transaction . " </span>";
            }

            return $header . "<div class='order-chat__details'>" . $body . '</div>';
        }

        if ($event->type == OrderEventHelper::TYPE_PAYMENT_RETURN) {
            $method = ArrayHelper::getValue($event->data, 'method');
            $amount = ArrayHelper::getValue($event->data, 'amount');
            $header = 'Возврат средств';

            $body = '';

            if ($method) {
                $body .= "<span class='order-chat__detail'>Способ: " . PaymentHelper::getMethodName($method) . "</span>";
            }
            if ($amount) {
                $body .= "<span class='order-chat__detail'>Сумма: " . (float)$amount . " ₸</span>";
            }

            return $header . "<div class='order-chat__details'>" . $body . '</div>';
        }

        // Payment error
        if ($event->type == OrderEventHelper::TYPE_PAYMENT_FAILURE) {
            $method = ArrayHelper::getValue($event->data, 'method');
            $amount = ArrayHelper::getValue($event->data, 'amount');
            $error = ArrayHelper::getValue($event->data, 'error');
            $type = ArrayHelper::getValue($event->data, 'type');
            $header = 'Ошибка при оплате';
            if ($type == PaymentHelper::TYPE_RETURN){
                $header = 'Ошибка при возврате средств';
            }

            if ($method == PaymentHelper::METHOD_BONUS) {
                $header = 'Ошибка при списание бонусов';
            }

            $body = '';

            if ($method) {
                $body .= "<span class='order-chat__detail'>Способ: " . PaymentHelper::getMethodName($method) . "</span>";
            }
            if ($amount) {
                $body .= "<span class='order-chat__detail'>Сумма: " . (float)$amount . "</span>";
            }
            if ($error) {
                $body .= "<span class='order-chat__detail'>Ошибка: " . $error . "</span>";
            }

            return $header . "<div class='order-chat__details'>" . $body . '</div>';
        }

        // Payment wait
        if ($event->type == OrderEventHelper::TYPE_PAYMENT_WAIT) {
            $method = ArrayHelper::getValue($event->data, 'method');
            $amount = ArrayHelper::getValue($event->data, 'amount');

            $header = 'Ожидаем онлайн-оплату';

            if ($method && $method == PaymentHelper::METHOD_BONUS) {
                $header = 'Ожидаем списание бонусов';
            }

            $body = '';

            if ($method) {
                $body .= "<span class='order-chat__detail'>Способ: " . PaymentHelper::getMethodName($method) . "</span>";
            }
            if ($amount) {
                $body .= "<span class='order-chat__detail'>Сумма: " . (float)$amount . " ₸</span>";
            }

            return $header . "<div class='order-chat__details'>" . $body . '</div>';
        }

        // Payment wait
        if ($event->type == OrderEventHelper::TYPE_ORDER_ACTIVATE) {
            $status = ArrayHelper::getValue($event->data, 'status');

            return 'Статус изменен на "' . $status . '" чтобы показать заказ на кассе';
        }

        return Html::encode($event->message);
    }

    /**
     * @param CareEvent $event
     * @return string
     * @throws Exception
     */
    public static function getCareMessage(CareEvent $event): string
    {
        if ($event->message){
            return $event->message;
        }

        return CareEventHelper::getTypeName($event->type);
    }

    /**
     * @param LeadEvent $event
     * @return string
     * @throws Exception
     */
    public static function getLeadMessage(LeadEvent $event): string
    {
        if ($event->type == LeadEventHelper::TYPE_JIVOSITE_FINISHED){
            return LeadEventHelper::getTypeName($event->type)
                . Html::tag('br')
                . Html::a('Прочить переписку', ['/lead/jivosite-messages', 'id' => $event->lead_id], [
                    'class' => 'js-view-modal'
                ]);
        }

        if ($event->message){
            return $event->message;
        }

        return LeadEventHelper::getTypeName($event->type);
    }

    /**
     * @param OrderEvent $event
     * @return string
     * @throws Exception
     */
    public static function assemblyChanges(OrderEvent $event): string
    {
        $message = $event->message;
        $reasonText = '';
        $products = ArrayHelper::getValue($event->data, 'products', []);

        if (!$products){
            return $message;
        }

        foreach ($products as $product) {
            $reasonText .= '<span class="order-chat__detail">sku: ' . ProductHelper::getCode($product['sku']) . ' - кол-во: ' . floor($product['quantity']) . ' остаток: ' . $product['stock'] . '</span>';
        }
        return $message . ': <span class="order-chat__details">' . $reasonText . '</span>';
    }

    /**
     * @param $name
     * @return string
     */
    public static function careTransfer($name): string
    {
        return "Обращение передано: $name";
    }

    /**
     * @param $name
     * @return string
     */
    public static function transferLead($name): string
    {
        return "Лид передан: $name";
    }

    /**
     * @param $name
     * @return string
     */
    public static function getShortName($name): string
    {
        $parts = explode(' ', $name);
        if (!$parts){
            return 'Без имени';
        }

        return implode(' ', array_slice($parts, 0, 2));
    }

    /**
     * @param Order $order
     * @return string
     */
    public static function orderTelegramNewMessage(Order $order): string
    {
        $message = 'Поступил новый заказ номер: ' . " <b>$order->number</b> \nКлиент: " . $order->name . ' Телефон: ' . $order->phone . "\nТовары:";

        $products = '';
        foreach ($order->products as $orderProduct) {
            $products .= "\n" . $orderProduct->name . ' Кол-во: ' . $orderProduct->quantity;
        }
        return $message . $products;
    }
}