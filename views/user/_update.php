<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use app\forms\UserUpdateForm;
use app\modules\auth\helpers\UserHelper;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model UserUpdateForm */

?>
<div class="modal__container">
    <div class="modal__title"><?= Html::encode($model->name) ?></div>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'validateOnChange' => false,
    ]); ?>
    <div class="modal__body">
        <div class="modal-form">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            <div class="modal-form__row modal-form__row--6">
                <?= $form->field($model, 'phone')->widget(MaskedInput::class, [
                    'mask' => '+7(999)999-99-99',
                    'options' => [
                        'disabled' => true,
                        'class' => 'form-control'
                    ]
                ]) ?>
                <?= $form->field($model, 'country')->textInput(['maxlength' => 3]) ?>
            </div>
            <div class="modal-form__row">
                <?= $form->field($model, 'role')->dropDownList($model->getRoleArray(), [
                    'prompt' => Yii::t('app', 'Select value')
                ]) ?>
                <?= $form->field($model, 'status')->dropDownList(UserHelper::getStatusArray()) ?>
            </div>

            <hr>

            <div class="modal-form__row">
                <?= $form->field($model, 'password', [
                    'template' => '{label}{input}{error}<span class="modal-form__input-link user-update-password-generator">Сгенерировать</span>'
                ])->textInput([
                    'maxlength' => true,
                    'autocomplete' => 'new-password'
                ]) ?>
                <?= $form->field($model, 'passwordRepeat')->textInput([
                    'maxlength' => true,
                    'autocomplete' => 'new-password'
                ]) ?>
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
<?php

$inputPassword = Html::getInputId($model, 'password');
$inputPasswordRepeat = Html::getInputId($model, 'passwordRepeat');

$js = <<<JS

$('.user-update-password-generator').click(function() {
    PasswordGenerator.generate(['#$inputPassword', '#$inputPasswordRepeat']) 
});

JS;

$this->registerJs($js);

?>
