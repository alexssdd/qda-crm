<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use app\forms\StoreUpdateForm;
use app\core\helpers\DataHelper;
use app\core\helpers\CityHelper;
use app\core\helpers\StoreHelper;
use app\core\helpers\MerchantHelper;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model StoreUpdateForm */

?>
<div class="modal__container modal__container--800">
    <div class="modal__title"><?= $model->name ?></div>
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'validateOnChange' => false,
    ]); ?>
    <div class="modal__body">
        <div class="modal-form">
            <div class="modal-form__row modal-form__row--3">
                <?= $form->field($model, 'name')->textInput() ?>
                <?= $form->field($model, 'name_short')->textInput() ?>
                <?= $form->field($model, 'type')->textInput(['disabled' => true]) ?>
            </div>
            <div class="modal-form__row modal-form__row--3">
                <?= $form->field($model, 'number')->textInput(['disabled' => true]) ?>
                <?= $form->field($model, 'status')->dropDownList(StoreHelper::getStatusArray()) ?>
                <?= $form->field($model, 'merchant_id')->dropDownList(MerchantHelper::getSelectArray(), [
                    'prompt' => Yii::t('app', 'Select value'),
                    'disabled' => true
                ]) ?>
            </div>
            <div class="modal-form__row modal-form__row--3">
                <?= $form->field($model, 'city_id')->dropDownList(CityHelper::getSelectArray(), [
                    'prompt' => Yii::t('app', 'Select value'),
                    'disabled' => true
                ]) ?>
                <?= $form->field($model, 'address')->textInput() ?>
                <?= $form->field($model, 'phone')->textInput()->widget(MaskedInput::class, [
                    'mask' => '+7(999)999-99-99',
                ]) ?>
            </div>
            <div class="modal-form__row modal-form__row--3">
                <?= $form->field($model, 'lat')->textInput() ?>
                <?= $form->field($model, 'lng')->textInput() ?>
                <?= $form->field($model, 'working_time')->textInput() ?>
            </div>
            <div class="modal-form__row modal-form__row--3">
                <?= $form->field($model, 'two_gis_id')->textInput() ?>
                <?= $form->field($model, 'yandex_company_id')->textInput() ?>
                <?= $form->field($model, 'google_id')->textInput() ?>
            </div>
            <div class="modal-form__row modal-form__row--3">
                <?= $form->field($model, 'delivery_number')->textInput() ?>
            </div>

            <h3 class="modal-form__heading">Каналы</h3>
            <div class="modal-form__row modal-form__row--4">
                <?= $form->field($model, 'kaspi_export')->dropDownList(DataHelper::getBoolArray()) ?>
                <?= $form->field($model, 'kaspi_id')->textInput() ?>
                <?= $form->field($model, 'ozon_export')->dropDownList(DataHelper::getBoolArray()) ?>
                <?= $form->field($model, 'ozon_id')->textInput() ?>
            </div>
            <div class="modal-form__row modal-form__row--4">
                <?= $form->field($model, 'wb_export')->dropDownList(DataHelper::getBoolArray()) ?>
                <?= $form->field($model, 'wb_id')->textInput() ?>
                <?= $form->field($model, 'wolt_export')->dropDownList(DataHelper::getBoolArray()) ?>
                <?= $form->field($model, 'wolt_id')->textInput() ?>
            </div>
            <div class="modal-form__row modal-form__row--4">
                <?= $form->field($model, 'glovo_export')->dropDownList(DataHelper::getBoolArray()) ?>
                <?= $form->field($model, 'glovo_id')->textInput() ?>
                <?= $form->field($model, 'ye_export')->dropDownList(DataHelper::getBoolArray()) ?>
                <?= $form->field($model, 'ye_id')->textInput() ?>
            </div>
            <div class="modal-form__row modal-form__row--4">
                <?= $form->field($model, 'halyk_export')->dropDownList(DataHelper::getBoolArray()) ?>
                <?= $form->field($model, 'halyk_id')->textInput() ?>
                <?= $form->field($model, 'jusan_export')->dropDownList(DataHelper::getBoolArray()) ?>
                <?= $form->field($model, 'jusan_id')->textInput() ?>
            </div>
            <div class="modal-form__row modal-form__row--4">
                <?= $form->field($model, 'forte_export')->dropDownList(DataHelper::getBoolArray()) ?>
                <?= $form->field($model, 'forte_id')->textInput() ?>
                <?= $form->field($model, 'app_id')->textInput() ?>
                <?= $form->field($model, 'ozon_id_mhv')->textInput() ?>
            </div>
            <div class="modal-form__row modal-form__row--4">
				<?= $form->field($model, 'wb_id_express')->textInput() ?>
				<?= $form->field($model, 'wb_id_pickup')->textInput() ?>
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
