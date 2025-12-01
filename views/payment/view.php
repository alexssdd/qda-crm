<?php

use yii\web\View;
use yii\helpers\Url;
use app\entities\Order;

/* @var $this View */
/* @var $token */
/* @var $order Order */
/* @var $methods [] */

$this->title = 'Выбор способа оплаты';
?>
<div class="page">
    <div class="page__header">
        <a class="page__back icon-keyboard_arrow_left" href="<?= Url::to(['payment/index', 'token' => $token]) ?>"></a>
        <h1 class="page__title"><?= $this->title ?></h1>
    </div>
    <div class="page__body">
        <div class="payment-list">
            <?php foreach ($methods as $method): ?>
                <a class="payment-list__item" href="<?= Url::to(['payment/widget', 'code' => $method['code'], 'token' => $token, 'type' => $method['type']]) ?>">
                    <?php if ($method['icon']) : ?>
                        <img class="payment-list__icon" src="<?= $method['icon'] ?>" alt="<?= $method['name'] ?>">
                    <?php endif; ?>
                    <span class="payment-list__info">
                        <span class="payment-list__name"><?= $method['name']?></span>
                        <span class="payment-list__additional">Комиссия: <?= $method['fee']?></span>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>