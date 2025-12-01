<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="page">
    <div class="page__header">
        <h1 class="page__title"><?= Html::encode($this->title) ?></h1>
    </div>
</div>
<div class="alert alert-danger">
    <?= nl2br(Html::encode($message)) ?>
</div>