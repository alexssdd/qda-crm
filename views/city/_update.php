<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\forms\CityUpdateForm;
use app\core\helpers\CityHelper;
use app\core\helpers\CountryHelper;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model CityUpdateForm */

?>
<div class="modal__container">
    <div class="modal__title"><?= $model->name ?></div>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'validateOnChange' => false,
    ]); ?>
    <div class="modal__body">
        <div class="modal-form">
            <div class="modal-form__row">
                <?= $form->field($model, 'name')->textInput() ?>
                <?= $form->field($model, 'name_kk')->textInput() ?>
            </div>
            <div class="modal-form__row">
                <?= $form->field($model, 'country_id')->dropDownList(CountryHelper::getSelectArray(), [
                    'prompt' => Yii::t('app', 'Select value')
                ]) ?>
                <?= $form->field($model, 'status')->dropDownList(CityHelper::getStatusArray()) ?>
            </div>
            <div class="modal-form__row">
                <?= $form->field($model, 'config[lat]')->label(Yii::t('app', 'Lat')) ?>
                <?= $form->field($model, 'config[lng]')->label(Yii::t('app', 'Lng')) ?>
            </div>
            <div class="modal-form__row modal-form__row--3">
                <?= $form->field($model, 'config[delivery_id]')->label(Yii::t('app', 'Logistic ID')) ?>
                <?= $form->field($model, 'config[kato]')->label(Yii::t('app', 'Kato')) ?>
                <?= $form->field($model, 'config[forte_id]')->label(Yii::t('app', 'Forte ID')) ?>
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
