<?php

use yii\web\View;
use yii\helpers\Url;
use app\entities\Order;

/* @var $this View */
/* @var $order Order */
/* @var $token */
/* @var $methods [] */

$this->title = 'Выбор банка';

?>
<div class="page">
    <div class="page__header">
        <h1 class="page__title"><?= $this->title ?></h1>
    </div>
    <div class="page__body">
        <div class="payment-list">
            <?php foreach ($methods as $method): ?>
                <a class="payment-list__item" href="<?= Url::to(['payment/view', 'code' => $method['code'], 'token' => $token]) ?>">
                    <?php if ($method['icon']) : ?>
                        <img class="payment-list__icon" src="<?= $method['icon'] ?>" alt="<?= $method['name'] ?>">
                    <?php endif; ?>
                    <span class="payment-list__info">
                        <span class="payment-list__name"><?= $method['name']?></span>
                    </span>
                    <i class="payment-list__arrow icon-keyboard_arrow_right"></i>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>