<?php

use yii\web\View;
use app\entities\Care;

/** @var $this View */
/** @var $care Care */

?>
<div class="care-history">
    <div class="care-history__top">
        <div class="care-history__heading">События</div>
    </div>
    <div class="care-history__body">
        <div class="care-chat">
            <?= $this->render('chat', ['care' => $care]) ?>
        </div>
    </div>
    <div class="care-history__footer">
        <div class="care-sms">
            <div class="care-sms__heading">SMS Сообщение</div>
            <select class="care-sms__select">
                <option value="1">Выборка 1</option>
                <option value="2">Выборка 2</option>
                <option value="3">Выборка 3</option>
                <option value="4">Выборка 4</option>
            </select>
            <input type="text" class="care-sms__input">
            <div class="care-sms__footer">
                <button type="button" class="care-sms__button btn btn--sm btn--success" onclick="Care.smsSend()">Отправить</button>
            </div>
        </div>
        <div class="care-call">
            <div class="care-call__heading">Звонки</div>
            <div class="care-call__items">
                <div class="care-call__item">
                    <a href="#" class="care-call__link">Уведомление клиенту</a>
                </div>
            </div>
        </div>
        <input class="care-history__input" type="text" placeholder="Напишите сообщение">
        <div class="care-history__actions">
            <button type="button" class="care-history__action care-history__sms" onclick="Care.smsToggle()">SMS</button>
            <button type="button" class="care-history__action care-history__call icon-call" onclick="Care.callToggle()"></button>
            <button type="button" class="care-history__action care-history__send icon-send" onclick="Care.chatSend()"></button>
        </div>
    </div>
</div>