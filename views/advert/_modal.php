<?php

use yii\web\View;
use app\entities\Advert;

/* @var $this View */
/* @var $advert Advert */

?>
<div class="modal__container modal__container--1000">
    <div class="modal__title"><?= $advert->name ?></div>
    <div class="modal__body">
        <div class="typography">
            <?= $advert->text ?>
        </div>
    </div>
    <div class="modal__footer modal__footer--bordered">
        <button class="modal__form_close btn btn--default advert__close" disabled onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></button>
    </div>
</div>
