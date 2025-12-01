<?php

use app\entities\Log;
use yii\helpers\Html;
use yii\helpers\Json;
use app\core\helpers\LogHelper;

/* @var $this yii\web\View */
/* @var $model Log */
?>
<div class="modal__container">
    <div class="modal__title">Log: <?= LogHelper::targetName($model->target) ?></div>
    <div class="modal__body">
        <div class="log-view">
            <div class="log-view__item">
                <span class="log-view__label">Status:</span>
                <?= LogHelper::statusLabel($model->status)?>
            </div>
            <div class="log-view__item">
                <span class="log-view__label">Created:</span>
                <span class="log-view__value"><?= Yii::$app->formatter->asDatetime($model->created_at)?></span>
            </div>
            <div class="log-view__item">
                <div class="log-view__label">Message:</div>
                <div class="log-view__value">
                    <pre class="log-view__message"><?= Html::encode(Json::encode($model->data))?></pre>
                </div>
            </div>
        </div>
    </div>
    <div class="modal__footer">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()">Закрыть</a>
    </div>
    <i class="modal__close icon-close"></i>
</div>
