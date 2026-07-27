<?php

use yii\helpers\Html;
use yii\web\View;
use app\core\helpers\UserHelper;
use app\modules\order\models\Order;
use app\modules\order\helpers\OrderHelper;

/* @var $order Order */
/* @var $this View */

$additionalFields = OrderHelper::getAdditionalFields($order);
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
    <div class="order-top__heading">Маршрут</div>
    <table class="order-products order-route">
        <thead>
            <tr>
                <th width="30">#</th>
                <th width="70">Направление</th>
                <th width="82">Страна</th>
                <th width="82">Нас. пункт</th>
                <th width="100">Метка</th>
                <th width="200">Адрес</th>
                <th width="150">Координаты</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Откуда</td>
                <td><?= Html::encode(OrderHelper::getFromCountry($order)) ?></td>
                <td><?= Html::encode(OrderHelper::getFromLocation($order)) ?></td>
                <td><?= Html::encode($order->from_name) ?></td>
                <td><?= Html::encode($order->from_address) ?></td>
                <td><?= Html::encode(OrderHelper::getFromCoordinates($order)) ?></td>
            </tr>
            <tr>
                <td>2</td>
                <td>Куда</td>
                <td><?= Html::encode(OrderHelper::getToCountry($order)) ?></td>
                <td><?= Html::encode(OrderHelper::getToLocation($order)) ?></td>
                <td><?= Html::encode($order->to_name) ?></td>
                <td><?= Html::encode($order->to_address) ?></td>
                <td><?= Html::encode(OrderHelper::getToCoordinates($order)) ?></td>
            </tr>
        </tbody>
    </table>
    <details class="order-technical">
        <summary class="order-technical__summary">Технические данные</summary>
        <table class="order-products order-technical__table">
            <thead>
                <tr>
                    <th>Поле</th>
                    <th>Значение</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>ID источника</td>
                    <td><?= Html::encode($order->source_id ?: '—') ?></td>
                </tr>
                <?php foreach ($additionalFields as $item): ?>
                    <tr>
                        <td><?= Html::encode($item['name']) ?></td>
                        <td><?= Html::encode($item['value']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </details>
</div>
