<?php

use yii\web\View;
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
        <?php if (OrderHelper::getShareUrl($order)): ?>
        <div class="order-header__item">
            <a href="#" class="order-header__share" title="Отправить заказ исполнителю">Поделиться</a>
        </div>
        <?php endif; ?>
    </div>
    <div class="order-header__right">
        <div class="order-header__time"></div>
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
