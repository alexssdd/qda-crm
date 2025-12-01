<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\entities\Order;
use app\core\rules\OrderRules;
use app\core\helpers\LeadHelper;
use app\core\helpers\PhoneHelper;
use app\core\helpers\OrderHelper;
use app\core\helpers\AddressSelectHelper;

/** @var $this View */
/** @var $order Order */

// Variables
$completed = OrderHelper::isCompleted($order->status);
$statuses = OrderHelper::getAvailableStatuses($order);
$statuses[$order->status] = OrderHelper::getStatusName($order->status);
$addressLabel = AddressSelectHelper::getLabel($order->address, $order->getAddressType(), $order->getAddressTitle());
$lead = OrderHelper::getLead($order);

?>
<?= $form = Html::beginForm(['order/update', 'id' => $order->id])?>
<div class="order-body">
    <div class="order-body__items">
        <div class="order-body__item">
            <label class="order-body__label">Клиент</label>
            <div class="order-body__block">
                <?= Html::textInput(null, $order->name, ['class' => 'order-body__input', 'readonly' => true])?>
                <?php if ($order->customer_id) : ?>
                <a href="<?= Url::to(['/customer/detail', 'id' => $order->customer_id]) ?>" class="order-body__input-icon js-view-modal icon-person"></a>
                <?php endif; ?>
            </div>
        </div>
        <div class="order-body__item">
            <label class="order-body__label">Телефон</label>
            <div class="order-body__block">
                <?= Html::textInput(null, OrderHelper::getPhoneWithExt($order), ['class' => 'order-body__input', 'readonly' => true])?>
                <a href="tel:+<?= $order->phone ?>" class="order-body__input-icon order-body__input-icon--blue icon-call"></a>
            </div>
        </div>
        <div class="order-body__item">
            <label class="order-body__label">Город</label>
            <div class="order-body__block">
                <?= Html::textInput(null, $order->city ? $order->city->name : null, ['class' => 'order-body__input', 'readonly' => true])?>
            </div>
        </div>
        <div class="order-body__item">
            <label class="order-body__label">Адрес</label>
            <div class="order-body__block">
                <div class="order-body__input order-body__address"><?= $addressLabel ?></div>
                <a href="<?= Url::to(['/order/address', 'id' => $order->id]) ?>" class="order-body__link js-view-modal">Редактировать</a>
            </div>
        </div>
        <div class="order-body__item">
            <label class="order-body__label">Доставка</label>
            <div class="order-body__block order-body__block--group">
                <?= Html::textInput(null, OrderHelper::getDeliveryLabel($order), ['class' => 'order-body__input', 'readonly' => true])?>
                <a href="<?= Url::to(['/order/delivery', 'id' => $order->id]) ?>" class="order-body__detail transition js-view-modal">Детали</a>
            </div>
        </div>
        <?php if ($courier = $order->courier) : ?>
        <div class="order-body__item">
            <label class="order-body__label">Курьер</label>
            <div class="order-body__block order-body__block--group">
                <?= Html::textInput(null, $courier->name, ['class' => 'order-body__input', 'readonly' => true])?>
                <a href="<?= Url::to(['/order/courier', 'id' => $order->id]) ?>" class="order-body__detail transition js-view-modal">Детали</a>
            </div>
        </div>
        <?php endif; ?>
        <div class="order-body__item">
            <label class="order-body__label">Оплата</label>
            <div class="order-body__block">
                <?= Html::textInput(null, OrderHelper::getPaymentLabel($order), ['class' => 'order-body__input', 'readonly' => true])?>
            </div>
        </div>
        <div class="order-body__item">
            <label class="order-body__label">Комментарий</label>
            <div class="order-body__block">
                <?= Html::textarea('comment', $order->comment, ['class' => 'order-body__input order-body__comment', 'readonly' => true])?>
            </div>
        </div>
        <?php if ($lead) : ?>
        <div class="order-body__item">
            <label class="order-body__label">Лид</label>
            <div class="order-body__block order-body__block--group">
                <?= Html::textInput(null, LeadHelper::getTitle($lead), ['class' => 'order-body__input', 'readonly' => true])?>
                <a href="<?= Url::to(['/lead/index', 'id' => $lead->id]) ?>" class="order-body__detail transition" target="_blank" data-pjax="0">Детали</a>
            </div>
        </div>
        <?php endif; ?>
        <div class="order-body__item">
            <label class="order-body__label">Статус</label>
            <div class="order-body__block">
                <?= Html::dropDownList('status', $order->status, $statuses, ['class' => 'order-body__input"'])?>
            </div>
        </div>
    </div>
    <div class="order-footer">
        <div class="order-actions">
            <div class="order-actions__heading">Дополнительные действия</div>
            <div class="order-actions__list">
                <div class="order-actions__item">
                    <a href="<?= Url::to(['/order/transfer', 'id' => $order->id]) ?>" class="order-actions__link js-view-modal">Передать заказ</a>
                </div>
                <?php if (!$completed && !$order->isPending()) : ?>
                <div class="order-actions__item">
                    <a href="<?= Url::to(['/order/pending', 'id' => $order->id]) ?>" class="order-actions__link js-view-modal">Отложить заказ</a>
                </div>
                <?php endif; ?>
                <?php if (OrderHelper::canActivate($order)) : ?>
                <div class="order-actions__item">
                    <a href="<?= Url::to(['/order/activate', 'id' => $order->id]) ?>" class="order-actions__link">Показать заказ на кассе</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="order-footer__left">
            <button class="btn btn--default" type="button" onclick="Order.actions()">Действия</button>
        </div>
        <div class="order-footer__right">
            <a href="<?= Url::to(['/order/cancel', 'id' => $order->id]) ?>" class="btn btn--warning order-body__button js-view-modal">Отменить</a>
            <?php if (OrderRules::canSave($order->status)): ?>
                <button type="submit" class="btn btn--success order-body__button">Сохранить</button>
            <?php endif;?>
        </div>
    </div>
</div>
<?= Html::endForm(); ?>