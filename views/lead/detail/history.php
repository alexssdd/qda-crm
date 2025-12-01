<?php

use app\entities\Lead;

/** @var $lead Lead */

?>
<div class="lead-history">
    <div class="lead-history__top">
        <div class="lead-history__heading">События</div>
    </div>
    <div class="lead-history__body">
        <div class="lead-chat">
            <?= $this->render('chat', ['lead' => $lead]) ?>
        </div>
    </div>
    <div class="lead-history__footer">
        <div class="lead-sms">
            <div class="lead-sms__heading">SMS Сообщение</div>
            <select class="lead-sms__select">
                <option value="1">Выборка 1</option>
                <option value="2">Выборка 2</option>
                <option value="3">Выборка 3</option>
                <option value="4">Выборка 4</option>
            </select>
            <input type="text" class="lead-sms__input">
            <div class="lead-sms__footer">
                <button type="button" class="lead-sms__button btn btn--sm btn--success" onclick="Lead.smsSend()">Отправить</button>
            </div>
        </div>
        <div class="lead-call">
            <div class="lead-call__heading">Звонки</div>
            <div class="lead-call__items">
                <div class="lead-call__item">
                    <a href="#" class="lead-call__link">Уведомление клиенту</a>
                </div>
            </div>
        </div>
        <input class="lead-history__input" type="text" placeholder="Напишите сообщение">
        <div class="lead-history__actions">
            <button type="button" class="lead-history__action lead-history__sms" onclick="Lead.smsToggle()">SMS</button>
            <button type="button" class="lead-history__action lead-history__call icon-call" onclick="Lead.callToggle()"></button>
            <button type="button" class="lead-history__action lead-history__send icon-send" onclick="Lead.chatSend()"></button>
        </div>
    </div>
</div>