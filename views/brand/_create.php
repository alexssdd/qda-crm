<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\forms\BrandCreateForm;
use app\core\helpers\BrandHelper;
use app\core\helpers\MerchantHelper;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model BrandCreateForm */

?>
<div class="modal__container">
    <div class="modal__title"><?= Yii::t('app', 'New brand')?></div>
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'validateOnChange' => false,
    ]); ?>
    <div class="modal__body">
        <div class="modal-form">
            <?= $form->field($model, 'name')->textInput() ?>
            <div class="modal-form__row">
                <?= $form->field($model, 'status')->dropDownList(BrandHelper::getStatusArray()) ?>
                <?= $form->field($model, 'merchant_id')->dropDownList(MerchantHelper::getSelectArray(), [
                    'prompt' => Yii::t('app', 'Select value')
                ]) ?>
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