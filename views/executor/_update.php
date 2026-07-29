<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\forms\ExecutorUpdateForm;

/* @var $this View */
/* @var $model ExecutorUpdateForm */

?>
<div class="modal__container modal__container--500">
    <div class="modal__title"><?= Html::encode($model->getExecutorName()) ?></div>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'validateOnChange' => false,
    ]); ?>
    <div class="modal__body">
        <div class="modal-form">
            <div class="modal-form__row">
                <?= $form->field($model, 'location_id')->dropDownList(
                    $model->getLocationOptions(),
                    ['prompt' => 'Не указана']
                ) ?>
            </div>
            <div class="modal-form__row">
                <?= $form->field($model, 'service_types')->checkboxList(
                    $model->getServiceOptions(),
                    ['class' => 'executor-services-edit']
                ) ?>
            </div>
        </div>
    </div>
    <div class="modal__footer">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()">
            <?= Yii::t('app', 'Close') ?>
        </a>
        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn--success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <i class="modal__close icon-close"></i>
</div>
