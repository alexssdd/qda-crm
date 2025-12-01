<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\core\helpers\CityHelper;
use app\core\helpers\MerchantHelper;
use app\forms\report\ReportCreateForm;
use app\forms\report\ReportDefecturaForm;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $paramsForm ReportDefecturaForm */
/* @var $createForm ReportCreateForm */

?>
<div class="modal__container">
    <div class="modal__title"><?= Yii::t('app', 'Create report')?></div>
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'validateOnChange' => false,
    ]); ?>
    <div class="modal__body">
        <div class="modal-form">
            <div class="modal-form__row">
                <?= $form->field($paramsForm, 'date_from')->input('date') ?>
                <?= $form->field($paramsForm, 'date_to')->input('date') ?>
            </div>
            <div class="modal-form__row">
                <?= $form->field($paramsForm, 'city_id')->dropDownList(CityHelper::getSelectArray(), [
                    'prompt' => Yii::t('app', 'All')
                ]) ?>
                <?= $form->field($paramsForm, 'merchant_id')->dropDownList(MerchantHelper::getSelectArray(), [
                    'prompt' => Yii::t('app', 'All')
                ]) ?>
            </div>
            <?= $form->field($createForm, 'comment')->textInput() ?>
        </div>
    </div>
    <div class="modal__footer">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
        <?= Html::submitButton(Yii::t('app', 'Create report'), ['class' => 'btn btn--success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <i class="modal__close icon-close"></i>
</div>