<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\core\helpers\DataHelper;
use app\forms\PriceTypeUpdateForm;
use app\core\helpers\PriceTypeHelper;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model PriceTypeUpdateForm */

?>
<div class="modal__container">
    <div class="modal__title"><?= $model->name ?></div>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'validateOnChange' => false,
    ]); ?>
    <div class="modal__body">
        <div class="modal-form">
            <?= $form->field($model, 'name')->textInput() ?>
            <div class="modal-form__row">
                <?= $form->field($model, 'type')->textInput(['disabled' => true]) ?>
                <?= $form->field($model, 'status')->dropDownList(PriceTypeHelper::getStatusArray()) ?>
            </div>
        </div>
    </div>
    <div class="modal__footer">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn--success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <i class="modal__close icon-close"></i>
</div>
