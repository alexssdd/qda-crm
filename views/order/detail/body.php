<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\core\rules\OrderRules;
use app\core\helpers\PhoneHelper;
use app\modules\order\models\Order;
use app\modules\order\helpers\OrderHelper;

/** @var $this View */
/** @var $order Order */

// Variables
$completed = OrderHelper::isCompleted($order->status);
$statuses = OrderHelper::getAvailableStatuses($order);
$statuses[$order->status] = OrderHelper::getStatusName($order->status);

?>
<?= $form = Html::beginForm(['order/update', 'id' => $order->id], 'post', ['class' => 'order-body-form'])?>
<div class="order-body">
    <div class="order-body__items">
        <div class="order-body__item">
            <label class="order-body__label">Клиент</label>
            <div class="order-body__block">
                <?= Html::textInput(null, $order->name, ['class' => 'order-body__input', 'readonly' => true])?>
            </div>
        </div>
        <div class="order-body__item">
            <label class="order-body__label">Телефон</label>
            <div class="order-body__block">
                <?= Html::textInput(null, PhoneHelper::getMaskPhone($order->phone), ['class' => 'order-body__input', 'readonly' => true])?>
                <?= Html::a('', 'tel:+' . PhoneHelper::getCleanNumber($order->phone), [
                    'class' => 'order-body__input-icon order-body__input-icon--blue icon-call',
                    'title' => 'Позвонить клиенту',
                    'aria-label' => 'Позвонить клиенту',
                ]) ?>
            </div>
        </div>
        <div class="order-body__item">
            <label class="order-body__label">Страна</label>
            <div class="order-body__block">
                <?= Html::textInput(null, $order->country ? $order->country->name : null, ['class' => 'order-body__input', 'readonly' => true])?>
            </div>
        </div>
        <div class="order-body__item">
            <label class="order-body__label">Канал</label>
            <div class="order-body__block">
                <?= Html::textInput(null, OrderHelper::getChannel($order->channel), ['class' => 'order-body__input', 'readonly' => true])?>
            </div>
        </div>

        <div class="order-body__item">
            <label class="order-body__label">Оплата</label>
            <div class="order-body__block">
                <?= Html::textInput(null, null, ['class' => 'order-body__input', 'readonly' => true])?>
            </div>
        </div>
        <div class="order-body__item order-body__item--separated">
            <label class="order-body__label">Комментарий</label>
            <div class="order-body__block">
                <?= Html::textarea('comment', $order->comment, ['class' => 'order-body__input order-body__comment', 'readonly' => true])?>
            </div>
        </div>
        <div class="order-body__item">
            <label class="order-body__label">Статус</label>
            <div class="order-body__block">
                <?= Html::dropDownList('status', $order->status, $statuses, ['class' => 'order-body__input'])?>
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
