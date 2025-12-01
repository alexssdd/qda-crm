<?php

use yii\web\View;
use yii\widgets\ActiveForm;
use app\forms\BrandUpdateForm;
use app\core\helpers\BrandHelper;
use app\core\helpers\MerchantHelper;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model BrandUpdateForm */

?>
<div class="modal__container">
    <div class="modal__title"><?= $model->name ?></div>
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'validateOnChange' => false,
        'fieldConfig' => [
            'inputOptions' => [
                'disabled' => true
            ]
        ]
    ]); ?>
    <div class="modal__body">
        <div class="modal-form">
            <?= $form->field($model, 'name')->textInput() ?>
            <div class="modal-form__row">
                <?= $form->field($model, 'status')->dropDownList(BrandHelper::getStatusArray()) ?>
                <?= $form->field($model, 'merchant_id')->dropDownList(MerchantHelper::getSelectArray(), [
                    'prompt' => ''
                ]) ?>
            </div>
        </div>
    </div>
    <div class="modal__footer">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
    </div>
    <?php ActiveForm::end(); ?>
    <i class="modal__close icon-close"></i>
</div>
