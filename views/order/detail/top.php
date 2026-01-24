<?php

use yii\web\View;
use app\core\helpers\UserHelper;
use app\core\helpers\OrderHelper;

/* @var $order Order */
/* @var $this View */

// Variables

?>
<ul class="product-context">
    <li><a class="product-context__link product-context__link--copy" href="#" onclick="navigator.clipboard.writeText(window.getSelection().toString())">Копировать</a></li>
    <!--<li><a class="product-context__link" href="#" onclick="Order.addAssemblyStock()">Добавить сборку</a></li>-->
    <li><a class="product-context__link" href="#" onclick="Order.addAssemblyAll()">Добавить сборку (все товары)</a></li>
    <?php if (UserHelper::isAdmin() || UserHelper::isAdministrator()): ?>
        <li><a class="product-context__link" href="#" onclick="Order.addAssemblyManual()">Добавить сборку (ручная)</a></li>
    <?php endif; ?>
    <li class="product-context__item-delimiter"></li>
    <!--<li><a class="product-context__link product-context__link--red" href="#" onclick="Order.removeAssembly()">Удалить сборку</a></li>-->
    <li><a class="product-context__link product-context__link--red" href="#" onclick="Order.removeAssemblyAll()">Удалить сборку (все товары)</a></li>
    <li class="product-context__item-delimiter"></li>
    <li><a class="product-context__link" href="#" onclick="Order.addProduct()">Добавить товар</a></li>
    <li><a class="product-context__link" href="#" onclick="Order.updateProducts()">Редактировать товары</a></li>
</ul>
<div class="order-top">
    <table class="order-products">
        <thead>
            <tr>
                <th width="30">#</th>
                <th width="82">SKU</th>
                <th>Товар</th>
                <th width="70">Цена</th>
                <th width="55">Кол-во</th>
                <th width="220">Сборка</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <div class="order-total">
        <div class="order-total__left"></div>
        <div class="order-total__right">
            <div class="order-total__items">
                <div class="order-total__item">
                    <div class="order-total__label">Сумма заказа</div>
                    <div class="order-total__divider"></div>
                    <div class="order-total__value"><?= '' ?> ₸</div>
                </div>
                <div class="order-total__item">
                    <div class="order-total__label">Доставка</div>
                    <div class="order-total__divider"></div>
                    <div class="order-total__value"></div>
                </div>
                <div class="order-total__item">
                    <div class="order-total__label">Итого</div>
                    <div class="order-total__divider"></div>
                    <div class="order-total__value"></div>
                </div>
            </div>
        </div>
    </div>
</div>