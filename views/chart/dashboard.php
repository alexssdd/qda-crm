<?php

use yii\web\View;

/** @var $this View */

$this->title = 'Дэшборд';

?>
<div class="chart-grid">
    <div class="chart-grid__column">
        <?= $this->render('sale/plan') ?>
    </div>
    <div class="chart-grid__column">
        <?= $this->render('order/delivery') ?>
        <?= $this->render('order/cancel') ?>
    </div>
</div>
<?php

$js = <<<JS

Chart.initDashboard();

JS;

$this->registerJs($js);

?>