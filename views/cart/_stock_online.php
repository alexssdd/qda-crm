<?php

use yii\web\View;
use app\entities\Product;
use app\core\helpers\StoreHelper;

/* @var $this View */
/* @var $stores[] */
/* @var $product Product */

?>
<div class="modal__container modal__container--1000">
    <div class="modal__title"><?= $product->name ?></div>
    <div class="modal__body">
        <table class="modal-table">
            <thead>
                <tr>
                    <th class="modal-table__td--180">Точка</th>
                    <th class="modal-table__td--85">Номер</th>
                    <th class="modal-table__td--85">Онлайн</th>
                    <th class="modal-table__td--85">Бронь</th>
                    <th class="modal-table__td--250">Адрес</th>
                    <th class="modal-table__td--150">Режим работы</th>
                    <th class="modal-table__td--132">Телефон</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($stores as $store) : ?>
                <tr>
                    <td class="modal-table__td--180">
                        <span class="modal-table__state modal-table__state--<?= $store['color']?>"></span>
                        <?= $store['name_short'] ?>
                        <span class="modal-table__small"><?= StoreHelper::getDurationDistanceLabel($store['duration'], $store['distance'])?></span>
                    </td>
                    <td class="modal-table__td--85"><?= $store['number'] ?></td>
                    <td class="modal-table__td--85"><?= $store['stock'] ?></td>
                    <td class="modal-table__td--85"></td>
                    <td class="modal-table__td--250"><?= $store['address']?></td>
                    <td class="modal-table__td--150"><?= $store['working_time']?></td>
                    <td class="modal-table__td--132"><?= $store['phone']?></td>
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
    </div>
    <i class="modal__close icon-close"></i>
</div>