<?php

use yii\web\View;
use app\entities\Order;
use yii\helpers\StringHelper;
use app\core\helpers\UserHelper;
use app\core\helpers\StoreHelper;
use app\core\helpers\OrderHelper;
use app\core\helpers\ProductHelper;
use app\core\helpers\OrderStoreHelper;

/* @var $order Order */
/* @var $this View */

// Variables
$bonusAmount = OrderHelper::getBonusAmount($order);
$bonusUsedAmount = OrderHelper::getBonusUsedAmount($order);

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
        <?php $i = 1; foreach ($order->products as $product) : ?>
            <tr oncontextmenu="return Order.contextProduct(event, <?= $order->id; ?>,<?= $product->id; ?>);" data-id="<?= $product->id; ?>" data-quantity="<?= (float)$product->quantity ?>">
                <td><?= $i++ ?></td>
                <td class="order-products__selectable"><?= ProductHelper::getCode($product->sku) ?></td>
                <td class="order-products__selectable" title="<?= $product->name ?>"><?= StringHelper::truncate($product->name, 75) ?></td>
                <td><?= Yii::$app->formatter->asDecimal(ProductHelper::getPrice($product->price)) ?></td>
                <td><?= OrderHelper::getQuantityLabel($product->quantity) ?></td>
                <td class="order-products__assemblies">
                    <?php foreach ($product->orderStoreProducts as $orderStoreProduct):
                        $orderStore = $orderStoreProduct->orderStore;
                        if ($orderStore->status == OrderStoreHelper::STATUS_CANCELED){
                            continue;
                        }
                        if ($orderStore->type == OrderStoreHelper::TYPE_MOVE && $orderStore->status == OrderStoreHelper::STATUS_COMPLETE){
                            continue;
                        }
                        ?>
                        <?php if ($orderStoreProduct->hasQuantity()): ?>
                            <span class="order-products__store" data-order-store="<?= $orderStoreProduct->order_store_id ?>">
                                <span class="order-products__store-value"><?= StoreHelper::getNameShort($orderStore->store)?>:<?= $orderStoreProduct->getQuantityLabel()?></span>
                                <?php if ($orderStore->status == OrderStoreHelper::STATUS_COMPLETE): ?>
                                    <i class="order-products__store-icon icon-check"></i>
                                <?php endif; ?>
                            </span>
                        <?php endif;?>
                    <?php endforeach;?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="order-total">
        <div class="order-total__left"></div>
        <div class="order-total__right">
            <div class="order-total__items">
                <?php if ($bonusAmount) : ?>
                    <div class="order-total__item order-total__item--blue">
                        <div class="order-total__label">Оплачено бонусами</div>
                        <div class="order-total__divider"></div>
                        <div class="order-total__value"><?= Yii::$app->formatter->asDecimal($bonusAmount) ?> ₸</div>
                    </div>
                <?php endif; ?>
                <div class="order-total__item">
                    <div class="order-total__label">Сумма заказа</div>
                    <div class="order-total__divider"></div>
                    <div class="order-total__value"><?= Yii::$app->formatter->asDecimal(OrderHelper::getAmount($order)) ?> ₸</div>
                </div>
                <?php if ($bonusUsedAmount) : ?>
                    <div class="order-total__item">
                        <div class="order-total__label">Использовано бонусов</div>
                        <div class="order-total__divider"></div>
                        <div class="order-total__value"><?= Yii::$app->formatter->asDecimal($bonusUsedAmount) ?> ₸</div>
                    </div>
                <?php endif; ?>
                <div class="order-total__item">
                    <div class="order-total__label">Доставка</div>
                    <div class="order-total__divider"></div>
                    <div class="order-total__value"><?= OrderHelper::getDeliveryCostLabel($order)?></div>
                </div>
                <div class="order-total__item">
                    <div class="order-total__label">Итого</div>
                    <div class="order-total__divider"></div>
                    <div class="order-total__value"><?= Yii::$app->formatter->asDecimal(OrderHelper::getAmountTotalWithBonus($order)) ?> ₸</div>
                </div>
            </div>
        </div>
    </div>
</div>