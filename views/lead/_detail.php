<?php

use app\entities\Lead;

/** @var $lead Lead */

?>
<div class="lead">
    <?= $this->render('detail/top', ['lead' => $lead]) ?>
    <div class="lead-detail">
        <div class="lead__left">
            <?= $this->render('detail/header', ['lead' => $lead]) ?>
            <?= $this->render('detail/body', ['lead' => $lead]) ?>
        </div>
        <div class="lead__right">
            <?= $this->render('detail/history', ['lead' => $lead]) ?>
        </div>
    </div>
</div>