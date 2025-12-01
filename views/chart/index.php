<?php

use yii\web\View;

/** @var $this View */

$this->title = 'Показатели';

?>
<div class="chart-grid">
    <div class="chart-grid__column">
        <?= $this->render('sale/channels') ?>
        <div class="chart-grid">
            <div class="chart-grid__column">
                <?= $this->render('sale/month') ?>
            </div>
            <div class="chart-grid__column">
                <?= $this->render('product/category') ?>
            </div>
        </div>
        <?= $this->render('delivery/average') ?>
    </div>
    <div class="chart-grid__column">
        <?= $this->render('sale/status') ?>
        <?= $this->render('sale/operator') ?>
        <div class="chart-grid">
            <div class="chart-grid__column">
                <?= $this->render('order/completed') ?>
                <?= $this->render('chat/count') ?>
            </div>
            <div class="chart-grid__column">
                <?= $this->render('order/handler') ?>
                <?= $this->render('order/average_handle') ?>
            </div>
        </div>
    </div>
</div>
<?php

$js = <<<JS

Chart.init();

JS;

$this->registerJs($js);

?>