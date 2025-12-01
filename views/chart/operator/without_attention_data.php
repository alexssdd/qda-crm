<?php

use yii\helpers\Url;

/** @var $data [] */

?>
<?php if ($data['count']) : ?>
    <div class="chart-circle">
        <div class="chart-circle__info">
            <div class="chart-circle__left">
                <div class="chart-circle__value"><?= $data['count'] ?></div>
            </div>
        </div>
        <div class="chart-circle__buttons">
            <?php foreach ($data['orders'] as $order) : ?>
                <a target="_blank" href="<?= Url::to(['/order/index', 'id' => $order['id']]) ?>" class="chart-circle__button">
                    <?= $order['number'] ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php else : ?>
    <div class="chart-success">
        <i class="icon-check chart-success__icon"></i>
    </div>
<?php endif; ?>