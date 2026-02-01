<?php

use yii\web\View;
use yii\helpers\Html;
use app\modules\order\models\Order;
use app\modules\order\helpers\OrderHelper;

/** @var $this View */
/** @var $order Order */

?>
<div class="order-header">
    <div class="order-header__left">
        <div class="order-header__items">
            <div class="order-header__item">
                <span class="order-header__label">Номер:</span>
                <span class="order-header__value"><?= $order->number ?></span>
            </div>
            <div class="order-header__item">
                <span class="order-header__label">Дата:</span>
                <span class="order-header__value"><?= OrderHelper::getCreated($order) ?></span>
            </div>
            <div class="order-header__item">
                <span class="order-header__label">Source ID:</span>
                <span class="order-header__value"><?= $order->source_id ?></span>
            </div>
        </div>
    </div>
    <div class="order-header__right">
        <div class="order-header__items">
        </div>
        <div class="order-header__time"></div>
    </div>
</div>
<?php

$seconds = time() - $order->created_at;
$stop = 'false';

if (OrderHelper::isCompleted($order->status)) {
    $stop = true;
    $seconds = $order->completed_at - $order->created_at;
}

$js = <<<JS

Order.id = $order->id;
Order.initTime($seconds, $stop);
Order.initCancel();

JS;

$this->registerJs($js);

?>