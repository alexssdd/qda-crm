<?php

use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use app\forms\StoreUpdateForm;
use app\core\helpers\CityHelper;
use app\core\helpers\StoreHelper;
use app\core\helpers\MerchantHelper;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model StoreUpdateForm */

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
            <div class="modal-form__row">
                <?= $form->field($model, 'name')->textInput() ?>
                <?= $form->field($model, 'name_short')->textInput() ?>
            </div>
            <div class="modal-form__row">
                <?= $form->field($model, 'merchant_id')->dropDownList(MerchantHelper::getSelectArray(), [
                    'prompt' => ''
                ]) ?>
                <?= $form->field($model, 'number')->textInput() ?>
            </div>
            <div class="modal-form__row">
                <?= $form->field($model, 'status')->dropDownList(StoreHelper::getStatusArray()) ?>
                <?= $form->field($model, 'city_id')->dropDownList(CityHelper::getSelectArray(), [
                    'prompt' => ''
                ]) ?>
            </div>
            <div class="modal-form__row">
                <?= $form->field($model, 'address')->textInput() ?>
                <?= $form->field($model, 'phone')->textInput()->widget(MaskedInput::class, [
                    'mask' => '+7(999)999-99-99',
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
