<?php

use yii\web\View;
use yii\helpers\Json;
use app\entities\Order;

/* @var $this View */
/* @var $data [] */
/* @var $params [] */
/* @var $order Order */
$this->title = Yii::t('app', 'Widget Page');
?>
<form action="https://kaspi.kz/online" method="post" id="kaspikz-form">
    <input type="hidden" name="TranId" value="212695">
    <input type="hidden" name="OrderId" value="212695">
    <input type="hidden" name="Amount" value="1200000">
    <input type="hidden" name="Service" value="test">
    <input type="hidden" name="returnUrl" value="https://test.kz/ru/order/kaspi-success?id=1234567">
    <button type="submit">Оплатить</button>
</form>
