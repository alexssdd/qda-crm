<?php

use yii\web\View;
use yii\helpers\Url;
use app\entities\Order;
use app\entities\Customer;
use app\core\helpers\PhoneHelper;
use app\core\helpers\OrderHelper;
use app\core\helpers\CustomerHelper;

/* @var $this View */
/* @var $detail [] */
/* @var $model Customer */

?>
<div class="modal__container modal__container--800">
    <div class="customer-modal">
        <div class="customer-modal__left">
            <div class="customer-modal__avatar">
                <img src="/images/default_user.png" alt="User" class="customer-modal__image">
                <i class="icon-star customer-modal__star customer-modal__star--green"></i>
            </div>
            <div class="customer-modal__name">
                <?= $model->name ?>
            </div>
            <div class="customer-modal__text">Зарегистрирован: <?= Yii::$app->formatter->asDatetime($model->created_at, 'php:d.m.Y H:i') ?></div>
            <div class="customer-modal__text">Город: <?= $detail['city'] ?></div>
            <div class="customer-modal__text">Телефон: <?= PhoneHelper::getMaskPhone($model->phone) ?></div>
            <div class="customer-modal__text">Почта: <?= $model->email ?></div>
            <div class="customer-modal__text">ИД в SL: <?= CustomerHelper::getVendorId($model) ?></div>
        </div>
        <div class="customer-modal__right">
            <div class="tabs">
                <div class="tabs__titles">
                    <div class="tabs__title tabs__title--active" data-tabs-target="#customerModalOrders">
                        История
                    </div>
                    <div class="tabs__title" data-tabs-target="#customerModalProducts">
                        Товары
                    </div>
                    <div class="tabs__title" data-tabs-target="#customerModalInfo">
                        О клиенте
                    </div>
                </div>
                <div class="tabs__blocks">
                    <div class="tabs__block tabs__block--active" id="customerModalOrders">
                        <div class="customer-orders">
                            <?php foreach ($detail['orders'] as $order) : /** @var $order Order */ ?>
                                <div class="customer-orders__item">
                                    <i class="customer-orders__icon icon-local_grocery_store"></i>
                                    <div class="customer-orders__body">
                                        <div class="customer-orders__date"><?= Yii::$app->formatter->asDatetime($order->created_at, 'php:d.m.Y H:i') ?></div>
                                        <div class="customer-orders__name">
                                            Заказ №<a class="customer-orders__link" href="<?= Url::to(['/order/index', 'id' => $order->id]) ?>" target="_blank"><?= $order->number ?></a> (<?= OrderHelper::getChannel($order->channel) ?>)
                                        </div>
                                        <div class="customer-orders__status"><?= OrderHelper::getStatusName($order->status) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="tabs__block" id="customerModalProducts">
                        <div class="customer-products">
                            <?php foreach ($detail['products'] as $product) : ?>
                                <div class="customer-products__item">
                                    <div class="customer-products__name"><?= $product['name'] ?></div>
                                    <div class="customer-products__brand">SKU: <?= $product['sku'] ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="tabs__block" id="customerModalInfo">
                        <div class="customer-info">
                            <textarea class="customer-info__textarea"></textarea>
                            <button type="button" class="customer-info__button btn btn--default">Сохранить</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <i class="modal__close icon-close"></i>
</div>