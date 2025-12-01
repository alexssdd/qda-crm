<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\core\helpers\DataHelper;
use app\forms\ProductUpdateForm;
use app\core\helpers\BrandHelper;
use app\core\helpers\ProductHelper;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model ProductUpdateForm */

?>
<div class="modal__container modal__container--800">
    <div class="modal__title"><?= $model->name ?></div>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'validateOnChange' => false,
    ]); ?>
    <div class="modal__body">
        <div class="modal-form">
            <?= $form->field($model, 'name')->textInput() ?>
            <div class="modal-form__row modal-form__row--3">
                <?= $form->field($model, 'brand_id')->dropDownList(BrandHelper::getSelectArray(), [
                    'prompt' => Yii::t('app', 'Select value')
                ]) ?>
                <?= $form->field($model, 'status')->dropDownList(ProductHelper::getStatusArray()) ?>
                <?= $form->field($model, 'sku')->textInput(['disabled' => true]) ?>
            </div>
            <h3 class="modal-form__heading">Каналы</h3>
            <div class="modal-form__row modal-form__row--3">
                <?= $form->field($model, 'export_kaspi')->dropDownList(DataHelper::getBoolArray(), ['prompt' => '']) ?>
                <?= $form->field($model, 'export_ozon')->dropDownList(DataHelper::getBoolArray(), ['prompt' => '']) ?>
                <?= $form->field($model, 'export_wb')->dropDownList(DataHelper::getBoolArray(), ['prompt' => '']) ?>
            </div>
            <div class="modal-form__row modal-form__row--3">
                <?= $form->field($model, 'export_wolt')->dropDownList(DataHelper::getBoolArray(), ['prompt' => '']) ?>
                <?= $form->field($model, 'export_glovo')->dropDownList(DataHelper::getBoolArray(), ['prompt' => '']) ?>
                <?= $form->field($model, 'export_ye')->dropDownList(DataHelper::getBoolArray(), ['prompt' => '']) ?>
            </div>
            <div class="modal-form__row modal-form__row--3">
                <?= $form->field($model, 'export_halyk')->dropDownList(DataHelper::getBoolArray(), ['prompt' => '']) ?>
                <?= $form->field($model, 'export_jusan')->dropDownList(DataHelper::getBoolArray(), ['prompt' => '']) ?>
                <?= $form->field($model, 'export_forte')->dropDownList(DataHelper::getBoolArray(), ['prompt' => '']) ?>
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