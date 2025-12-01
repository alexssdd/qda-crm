<?php

use yii\web\View;
use app\entities\Lead;
use yii\helpers\ArrayHelper;
use app\core\helpers\LeadHelper;

/* @var $this View */
/* @var $lead Lead */
/* @var $chat [] */

$messages = ArrayHelper::getValue($chat, 'messages', []);

?>
<div class="modal__container modal__container--800">
    <div class="modal__title">Переписка лида: <?= LeadHelper::getTitle($lead) ?></div>
    <div class="modal__body">
        <?php if ($messages) : ?>
            <div class="modal-chat">
                <?php foreach ($messages as $message) : ?>
                <div class="modal-chat__item modal-chat__item--<?= $message['type'] == 'agent' ? 'operator' : 'client' ?>">
                    <?php if ($message['type'] == 'visitor') : ?>
                        <?php if ($chat['visitor_image']) : ?>
                            <img src="<?= $chat['visitor_image'] ?>" alt="<?= $chat['visitor'] ?>" class="modal-chat__image">
                        <?php else : ?>
                            <i class="modal-chat__user icon-person"></i>
                        <?php endif; ?>
                    <?php endif; ?>
                    <div class="modal-chat__block">
                        <div class="modal-chat__author">
                            <?php if ($message['type'] == 'visitor') {
                                echo $chat['visitor'];
                            } else {
                                echo ArrayHelper::getValue($chat['agents'], $message['agent_id'] . '.name', 'Оператор');
                            } ?>
                        </div>
                        <div class="modal-chat__text"><?= $message['message'] ?></div>
                    </div>
                    <div class="modal-chat__date"><?= date('d.m.y H:i', $message['timestamp']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
        Переписка не найдена
        <?php endif; ?>
    </div>
    <div class="modal__footer modal__footer--bordered">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
    </div>
    <i class="modal__close icon-close"></i>
</div>