<?php

use yii\web\View;

/** @var $this View */

$this->title = 'Мониторинг';

?>
<div class="chart-grid">
    <div class="chart-grid__column">
        <?= $this->render('operator/long_handle') ?>
        <div class="chart-grid">
            <div class="chart-grid__column">
                <?= $this->render('operator/without_attention') ?>
            </div>
            <div class="chart-grid__column">
                <?= $this->render('operator/standard_long') ?>
            </div>
        </div>
    </div>
    <div class="chart-grid__column">
        <div class="chart-grid">
            <div class="chart-grid__column">
                <?= $this->render('operator/express_search') ?>
            </div>
            <div class="chart-grid__column">
                <?= $this->render('operator/express_long') ?>
            </div>
        </div>
    </div>
</div>
<?php

$js = <<<JS

Chart.initOperator();

JS;

$this->registerJs($js);

?>