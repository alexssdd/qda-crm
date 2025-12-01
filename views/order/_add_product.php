<?php

use yii\web\View;
use yii\helpers\Html;
use app\entities\Order;

/* @var $this View */
/* @var $order Order */
/* @var $products string */

?>
<div class="modal__container modal__container--1000 order-add-product">
    <div class="modal__title">Добавление нового товара к заказу</div>
    <?= Html::beginForm(['order/add-product', 'id' => $order->id], 'post', ['class' => 'order-add-product__form'])?>
    <?= Html::hiddenInput('product_id', null, ['class' => 'order-add-product__product-id']) ?>
    <?= Html::hiddenInput('quantity', null, ['class' => 'order-add-product__quantity']) ?>
    <div class="modal__body">
        <div class="form-group modal-form__search">
            <input class="modal-form__search-input order-add-product__input" type="text" placeholder="Введите название товара">
            <i class="modal-form__search-icon icon-search"></i>
        </div>
        <div class="order-add-product__products">
            <table class="modal-table modal-table--striped">
                <thead>
                <tr>
                    <th class="modal-table__td--85 text-center">Код</th>
                    <th class="modal-table__td--100 text-center">Штрихкод</th>
                    <th class="modal-table__td--377">Товар</th>
                    <th class="modal-table__td--150 text-center">Бренд</th>
                    <th class="modal-table__td--85 text-center">Остаток</th>
                    <th class="modal-table__td--85 text-center">Количество</th>
                    <th class="modal-table__td--85 text-center">Цена</th>
                </tr>
                </thead>
                <tbody><?= $products ?></tbody>
            </table>
            <div class="order-add-product__loader">
                <div class="order-add-product__loader-circle"></div>
            </div>
        </div>
    </div>
    <div class="modal__footer modal__footer--bordered">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
        <?= Html::submitButton('Добавить', ['class' => 'btn btn--success order-add-product__submit', 'disabled' => true]) ?>
    </div>
    <?= Html::endForm()?>
    <i class="modal__close icon-close"></i>
</div>