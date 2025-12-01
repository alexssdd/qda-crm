<?php

/** @var $data [] */

?>
<div class="chart-count">
    <div class="chart-count__list">
        <div class="chart-count__item chart-count__item--green">
            <div class="chart-count__label">Активных</div>
            <div class="chart-count__value"><?= $data['active'] ?></div>
        </div>
        <div class="chart-count__item">
            <div class="chart-count__label">Завершенных</div>
            <div class="chart-count__value"><?= $data['finished'] ?></div>
        </div>
    </div>
</div>