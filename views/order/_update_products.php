<?php

use yii\web\View;
use yii\helpers\Html;
use app\entities\Order;
use yii\helpers\StringHelper;
use app\core\helpers\OrderHelper;
use app\core\helpers\ProductHelper;

/* @var $this View */
/* @var $order Order */

?>
<div class="modal__container modal__container--1000">
    <div class="modal__title">Редактирование товаров</div>
    <?php if (!OrderHelper::canUpdate($order)): ?>
        <div class="modal__alert modal__alert-danger">Заказ больже нельзая редактировать</div>
    <?php elseif (OrderHelper::canUpdateSingle($order)): ?>
        <div class="modal__alert modal__alert-warning">Заказ можно редактировать только один раз !!!</div>
    <?php endif; ?>
    <?= Html::beginForm(['order/update-products', 'id' => $order->id])?>
    <div class="modal__body">
        <table class="modal-table modal-table--striped">
            <thead>
            <tr>
                <th class="modal-table__td--85">Код</th>
                <th class="modal-table__td--627">Товар</th>
                <th class="modal-table__td--85">Кол-во</th>
                <th class="modal-table__td--85">Кол-во орг</th>
                <th class="modal-table__td--85">Цена</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($order->products as $product) : ?>
                <tr>
                    <td class="modal-table__td--85"><?= ProductHelper::getCode($product->sku) ?></td>
                    <td class="modal-table__td--627" title="<?= $product->name ?>"><?= StringHelper::truncate($product->name, 90) ?></td>
                    <td class="modal-table__td--85">
                        <input type="number" class="modal-table__input" name="<?= 'products[' . $product->id . ']'?>" value="<?= OrderHelper::getQuantity($product->quantity) ?>" <?= !OrderHelper::canUpdate($order) ? 'disabled': ''?> min="0">
                    </td>
                    <td class="modal-table__td--85"><?= OrderHelper::getQuantity($product->quantity_original) ?></td>
                    <td class="modal-table__td--85"><?= ProductHelper::getPrice($product->price)?></td>
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