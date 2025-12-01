<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\forms\AddressCreateForm;
use app\core\helpers\CityHelper;
use app\core\helpers\AddressHelper;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model AddressCreateForm */

?>
<div class="modal__container">
    <div class="modal__title"><?= Yii::t('app', 'New address')?></div>
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'validateOnChange' => false,
    ]); ?>
    <div class="modal__body">
        <div class="modal-form">
            <?= $form->field($model, 'customer_name')->textInput(['disabled' => true]) ?>
            <?= $form->field($model, 'address')->textInput() ?>
            <div class="modal-form__row">
                <?= $form->field($model, 'city_id')->dropDownList(CityHelper::getSelectArray(), [
                    'prompt' => Yii::t('app', 'Select value')
                ]) ?>
                <?= $form->field($model, 'status')->dropDownList(AddressHelper::getStatusArray()) ?>
            </div>
            <div class="modal-form__row">
                <?= $form->field($model, 'lat')->textInput() ?>
                <?= $form->field($model, 'lng')->textInput() ?>
            </div>
        </div>
    </div>
    <div class="modal__footer">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
        <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn--success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <i class="modal__close icon-close"></i>
</div>