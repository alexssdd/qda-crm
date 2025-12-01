<?php

use yii\web\View;
use yii\helpers\Html;
use app\entities\OrderProduct;

/* @var $this View */
/* @var $stores [] */
/* @var $orderProduct OrderProduct */
?>
<div class="modal__container modal__container--1200">
    <div class="modal__title">Ручная сборка товара: <?= $orderProduct->name ?></div>
    <?= Html::beginForm(['order/add-assembly-manual', 'orderProductId' => $orderProduct->id])?>
    <div class="modal__body">
        <table class="modal-table">
            <thead>
            <tr>
                <th class="modal-table__td--250">Точка</th>
                <th class="modal-table__td--85">Номер</th>
                <th class="modal-table__td--85">Онлайн</th>
                <th class="modal-table__td--85">Бронь</th>
                <th class="modal-table__td--85">Сборка</th>
                <th class="modal-table__td--277">Адрес</th>
                <th class="modal-table__td--150">Режим работы</th>
                <th class="modal-table__td--150">Телефон</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($stores as $store) : ?>
                <tr>
                    <td class="modal-table__td--250">
                        <span class="modal-table__state modal-table__state--<?= $store['color']?>"></span>
                        <?= $store['name_short'] ?>
                    </td>
                    <td class="modal-table__td--85"><?= $store['number'] ?></td>
                    <td class="modal-table__td--85"><?= $store['stock'] ?></td>
                    <td class="modal-table__td--85"></td>
                    <td class="modal-table__td--85">
                        <input type="number" class="modal-table__input" name="quantities[<?= $store['id']?>]" value="<?= $store['assembly']?>">
                    </td>
                    <td class="modal-table__td--277 modal-table__break"><?= $store['address']?></td>
                    <td class="modal-table__td--150"><?= $store['working_time']?></td>
                    <td class="modal-table__td--150"><?= $store['phone']?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="modal__footer modal__footer--bordered">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn--success']) ?>
    </div>
    <?= Html::endForm()?>
    <i class="modal__close icon-close"></i>
</div>