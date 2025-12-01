<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\core\helpers\CityHelper;
use app\core\helpers\LeadHelper;
use app\services\OperatorService;
use app\core\helpers\BrandHelper;
use app\forms\report\ReportLeadForm;
use app\forms\report\ReportCreateForm;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $paramsForm ReportLeadForm */
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
                <?= $form->field($paramsForm, 'channel')->dropDownList(LeadHelper::getChannelArray(), [
                    'prompt' => Yii::t('app', 'All')
                ]) ?>
            </div>
            <div class="modal-form__row">
                <?= $form->field($paramsForm, 'brand_id')->dropDownList(BrandHelper::getSelectArray(), [
                    'prompt' => Yii::t('app', 'All')
                ]) ?>
                <?= $form->field($paramsForm, 'handler_id')->dropDownList($users, [
                    'prompt' => Yii::t('app', 'All')
                ]) ?>
            </div>
            <div class="modal-form__row">
                <?= $form->field($paramsForm, 'status')->dropDownList(LeadHelper::getStatusArray(), [
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