<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\modules\order\models\Order;
use app\modules\order\helpers\OrderHelper;

/** @var $this View */
/** @var $order Order */

?>
<div class="order-header">
    <div class="order-header__meta">
        <div class="order-header__item">
            <span class="order-header__label">Номер:</span>
            <span class="order-header__value"><?= Html::encode((string) $order->number) ?></span>
        </div>
        <div class="order-header__item">
            <span class="order-header__label">Дата:</span>
            <span class="order-header__value"><?= OrderHelper::getCreated($order) ?></span>
        </div>
        <div class="order-header__item">
            <span class="order-header__label">Ответственный:</span>
            <span class="order-header__value" title="<?= Html::encode($order->handler?->name ?: 'Бот') ?>"><?= Html::encode($order->handler?->name ?: 'Бот') ?></span>
        </div>
    </div>
    <div class="order-header__right">
        <div class="order-header__time"></div>
        <?php if (OrderHelper::getShareUrl($order)): ?>
            <a href="<?= Url::to(['/order/share', 'id' => $order->id]) ?>"
               class="order-header__share js-view-modal"
               title="Отправить заказ исполнителю">
                <svg class="order-header__share-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="18" cy="5" r="3"></circle>
                    <circle cx="6" cy="12" r="3"></circle>
                    <circle cx="18" cy="19" r="3"></circle>
                    <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                    <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                </svg>
                Поделиться
            </a>
        <?php endif; ?>
    </div>
</div>
<?php

$seconds = time() - $order->created_at;
$stop = 'false';

if (OrderHelper::isCompleted($order->status)) {
    $stop = true;
    $seconds = max(0, ($order->completed_at ?: $order->created_at) - $order->created_at);
}

$js = <<<JS

Order.id = $order->id;
Order.initTime($seconds, $stop);
Order.initCancel();
Order.initShare();

JS;

$this->registerJs($js);

?>
