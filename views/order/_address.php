<?php

use app\entities\Order;
use app\widgets\AddressSelectWidget;

/** @var $order Order */

?>
<?= AddressSelectWidget::widget([
    'order' => $order,
    'action' => ['order/address-save', 'id' => $order->id],
    'doneCallback' => 'function(){}'
]) ?>