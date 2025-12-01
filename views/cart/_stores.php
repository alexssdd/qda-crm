<?php

use yii\web\View;
use yii\helpers\Html;

/* @var $stores [] */
/* @var $this View */
/* @var $error string */

?>
<div class="modal__container modal__container--1000">
    <div class="modal__title">Сборка всех товаров в одну точку продаж</div>
    <div class="modal__body">
        <?php if ($error) : ?>
        <div class="modal__alert modal__alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <table class="modal-table">
            <thead>
                <tr>
                    <th class="modal-table__td--350">Точка</th>
                    <th class="modal-table__td--250">Адрес</th>
                    <th class="modal-table__td--150">Режим работы</th>
                    <th class="modal-table__td--221">Телефон</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stores as $item) : ?>
                <tr class="modal-table__tr--<?= $item['color'] ?>" data-id="<?= $item['id'] ?>" data-name="<?= $item['name_short'] ?>">
                    <td class="modal-table__td--350<?= $item['color'] == 'gray' ? '' : ' modal-table__selector' ?>">
                        <span class="modal-table__state modal-table__state--<?= $item['color'] ?>"></span>
                        <?= $item['name_short'] ?>
                    </td>
                    <td class="modal-table__td--250 modal-table__break"><?= $item['address'] ?></td>
                    <td class="modal-table__td--150"><?= $item['working_time'] ?></td>
                    <td class="modal-table__td--221"><?= $item['phone'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="modal__footer modal__footer--bordered">
        <div class="modal__footer-left">
            <div class="assembly-legends">
                Все товары в точке продажи
                <span class="assembly-legends__state assembly-legends__state--green"></span>
                с запасом
                <span class="assembly-legends__state assembly-legends__state--yellow"></span>
                последние
                <span class="assembly-legends__state assembly-legends__state--gray"></span>
                нет только по частям
            </div>
        </div>
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.closeAdditional()"><?= Yii::t('app', 'Close'); ?></a>
        <?= Html::button('Сохранить', ['class' => 'btn btn--success cart-stores__button', 'onclick' => 'Cart.storeSave()']) ?>
    </div>
    <i class="modal__close icon-close"></i>
</div>