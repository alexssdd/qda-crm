<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\forms\order\OrderCancelForm;

/* @var $this View */
/* @var $model OrderCancelForm */

?>
<div class="modal__container">
    <div class="modal__title">Отложить заказ</div>
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'validateOnChange' => false,
    ]); ?>
    <div class="modal__body">
        <div class="modal-form">
            <?= $form->field($model, 'reason')->dropDownList([
                'Точка продажи закрыта' => 'Точка продажи закрыта',
                'Нет курьера' => 'Нет курьера',
                'Перенос клиентом' => 'Перенос клиентом',
            ], [
                'prompt' => Yii::t('app', 'Select value')
            ]) ?>
        </div>
    </div>
    <div class="modal__footer">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
        <?= Html::submitButton('Отложить', ['class' => 'btn btn--success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <i class="modal__close icon-close"></i>
</div>