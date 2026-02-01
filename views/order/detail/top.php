<?php

use yii\helpers\Html;
use yii\web\View;
use app\core\helpers\UserHelper;
use app\modules\order\models\Order;
use app\modules\order\helpers\OrderHelper;

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
                <th width="30">Direction</th>
                <th width="82">Country</th>
                <th width="82">Location</th>
                <th width="100">Label</th>
                <th width="200">Address</th>
                <th width="150">Lat, Lng</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>From</td>
                <td><?= OrderHelper::getFromCountry($order) ?></td>
                <td><?= OrderHelper::getFromLocation($order) ?></td>
                <td><?= Html::encode($order->from_name) ?></td>
                <td><?= Html::encode($order->from_address) ?></td>
                <td><?= OrderHelper::getFromCoordinates($order) ?></td>
            </tr>
        <tr>
            <td>2</td>
            <td>To</td>
            <td><?= OrderHelper::getToCountry($order) ?></td>
            <td><?= OrderHelper::getToLocation($order) ?></td>
            <td><?= Html::encode($order->to_name) ?></td>
            <td><?= Html::encode($order->to_address) ?></td>
            <td><?= OrderHelper::getToCoordinates($order) ?></td>
        </tr>
        </tbody>
    </table>
    <div class="order-total">
        <div class="order-total__left"></div>
        <div class="order-total__right">

        </div>
    </div>
</div>