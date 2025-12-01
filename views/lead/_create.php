<?php

use yii\web\View;

/* @var $this View */
/* @var $products string */

?>
<div class="modal__container modal__container--1000">
    <div class="modal__title">Новый лид</div>
    <div class="modal__body"></div>
    <div class="modal__footer modal__footer--bordered">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
    </div>
    <i class="modal__close icon-close"></i>
</div>