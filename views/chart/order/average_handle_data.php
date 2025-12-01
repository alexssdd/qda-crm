<?php

use app\core\helpers\DateHelper;

/** @var $data [] */

?>
<div class="chart-text animated fadeIn">
    <div class="chart-text__value"><?= DateHelper::getGmDate($data['time_average']) ?></div>
</div>