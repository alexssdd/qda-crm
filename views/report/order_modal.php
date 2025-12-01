<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\core\helpers\CityHelper;
use app\core\helpers\OrderHelper;
use app\services\OperatorService;
use app\core\helpers\PaymentHelper;
use app\core\helpers\DeliveryHelper;
use app\forms\report\ReportOrderForm;
use app\forms\report\ReportCreateForm;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $paramsForm ReportOrderForm */
/* @var $createForm ReportCreateForm */

// Variables
$users = (new OperatorService())->getAll();

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
                <?= $form->field($paramsForm, 'channel')->dropDownList(OrderHelper::getChannels(), [
                    'prompt' => Yii::t('app', 'All')
                ]) ?>
            </div>
            <div class="modal-form__row">
                <?= $form->field($paramsForm, 'delivery_method')->dropDownList(DeliveryHelper::getMethods(), [
                    'prompt' => Yii::t('app', 'All')
                ]) ?>
                <?= $form->field($paramsForm, 'payment_method')->dropDownList(PaymentHelper::getMethods(), [
                    'prompt' => Yii::t('app', 'All')
                ]) ?>
            </div>
            <div class="modal-form__row">
                <?= $form->field($paramsForm, 'handler_id')->dropDownList($users, [
                    'prompt' => Yii::t('app', 'All')
                ]) ?>
                <?= $form->field($paramsForm, 'status')->dropDownList(OrderHelper::getStatuses(), [
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