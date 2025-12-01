<?php

use app\widgets\AddressSelectWidget;

/** @var $attributes array */

?>
<?= AddressSelectWidget::widget([
    'action' => ['cart/address-save'],
    'attributes' => $attributes,
    'doneCallback' => 'Cart.addressSelectFinish'
]) ?>