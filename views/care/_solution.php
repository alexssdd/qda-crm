<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\forms\care\CareSolutionForm;

/* @var $this View */
/* @var $model CareSolutionForm */

?>
<div class="modal__container">
    <div class="modal__title">Подробно описать решение</div>
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'validateOnChange' => false,
        'fieldConfig' => [
            'labelOptions' => ['class' => 'form-label']
        ]
    ]); ?>
    <div class="modal__body">
        <div class="modal-form">
            <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>
        </div>
    </div>
    <div class="modal__footer">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn--success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <i class="modal__close icon-close"></i>
</div>