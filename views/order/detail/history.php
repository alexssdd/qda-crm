<?php

use yii\web\View;
use yii\helpers\Html;
use app\entities\Order;
use app\core\helpers\OrderNotifyHelper;

/** @var $this View */
/** @var $order Order */

?>
<div class="order-history">
    <div class="order-history__top">
        <div class="order-history__heading">События</div>
    </div>
    <div class="order-history__body">
        <div class="order-chat">
            <?= $this->render('chat', ['order' => $order]) ?>
        </div>
    </div>
    <div class="order-history__footer">
        <div class="order-whatsapp">
            <div class="order-whatsapp__heading">WhatsApp Сообщение</div>
            <?= Html::dropDownList('', null, OrderNotifyHelper::getWhatsappTemplates(), [
                'class' => 'order-whatsapp__select order-whatsapp__template',
                'prompt' => 'Выберите шаблон'
            ]) ?>
            <input type="text" class="order-whatsapp__input">
            <div class="order-whatsapp__footer">
                <button type="button" class="order-whatsapp__button btn btn--sm btn--success" onclick="Order.whatsappSend()">Отправить</button>
            </div>
        </div>
        <div class="order-sms">
            <div class="order-sms__heading">SMS Сообщение</div>
            <select class="order-sms__select">
                <option value="1">Выборка 1</option>
                <option value="2">Выборка 2</option>
                <option value="3">Выборка 3</option>
                <option value="4">Выборка 4</option>
            </select>
            <input type="text" class="order-sms__input">
            <div class="order-sms__footer">
                <button type="button" class="order-sms__button btn btn--sm btn--success" onclick="Order.smsSend()">Отправить</button>
            </div>
        </div>
        <div class="order-call">
            <div class="order-call__heading">Звонки</div>
            <div class="order-call__items">
                <div class="order-call__item">
                    <a href="#" class="order-call__link">Уведомление клиенту</a>
                </div>
                <div class="order-call__item">
                    <a href="#" class="order-call__link">Уведомление точкам продаж</a>
                </div>
            </div>
        </div>
        <input class="order-history__input" type="text" placeholder="Напишите сообщение">
        <div class="order-history__actions">
            <button type="button" class="order-history__action order-history__whatsapp icon-whatsapp" onclick="Order.whatsappToggle()"></button>
            <button type="button" class="order-history__action order-history__sms" onclick="Order.smsToggle()">SMS</button>
            <button type="button" class="order-history__action order-history__call icon-call" onclick="Order.callToggle()"></button>
            <button type="button" class="order-history__action order-history__send icon-send" onclick="Order.chatSend()"></button>
        </div>
    </div>
</div>