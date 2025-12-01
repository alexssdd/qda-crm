<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use app\forms\AdvertCreateForm;
use app\core\helpers\AdvertHelper;
use vova07\imperavi\Widget as Imperavi;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model AdvertCreateForm */

?>
<div class="modal__container modal__container--1000">
    <div class="modal__title"><?= Yii::t('app', 'New advert')?></div>
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'validateOnChange' => false,
    ]); ?>
    <div class="modal__body">
        <div class="modal-form">
            <?= $form->field($model, 'name')->textInput() ?>
            <div class="modal-form__row modal-form__row--3">
                <?= $form->field($model, 'status')->dropDownList(AdvertHelper::getStatusArray()) ?>
                <?= $form->field($model, 'begin_at')->widget(MaskedInput::class, [
                    'mask' => '99.99.9999 99:99'
                ]) ?>
                <?= $form->field($model, 'end_at')->widget(MaskedInput::class, [
                    'mask' => '99.99.9999 99:99'
                ]) ?>
            </div>
            <?= $form->field($model, 'text')->widget(Imperavi::class, [
                'settings' => [
                    'lang' => 'ru',
                    'replacedivs' => false,
                    'minHeight' => 250,
                    'imageUpload' => Url::to(['/advert/image-upload']),
                    'fileUpload' => Url::to(['/advert/file-upload']),
                    'plugins' => [
                        'clips',
                        'fullscreen',
                        'fontcolor',
                        'fontsize',
                        'table',
                        'video',
                        'imagemanager',
                        'filemanager',
                    ],
                ],
            ]) ?>
        </div>
    </div>
    <div class="modal__footer">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
        <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn--success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <i class="modal__close icon-close"></i>
</div>