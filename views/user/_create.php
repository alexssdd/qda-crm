<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use app\forms\UserCreateForm;
use app\core\helpers\UserHelper;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model UserCreateForm */

?>
<div class="modal__container">
    <div class="modal__title"><?= Yii::t('app', 'New User')?></div>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'validateOnChange' => false,
    ]); ?>
    <div class="modal__body">
        <div class="modal-form">
            <?= $form->field($model, 'full_name')->textInput() ?>
            <div class="modal-form__row modal-form__row--6">
                <?= $form->field($model, 'phone')->widget(MaskedInput::class, [
                    'mask' => '+7(999)999-99-99',
                ]) ?>
                <?= $form->field($model, 'telegram_id')->textInput() ?>
            </div>
            <div class="modal-form__row">
                <?= $form->field($model, 'role')->dropDownList(UserHelper::getRoleArray(), [
                    'prompt' => Yii::t('app', 'Select value')
                ]) ?>
                <?= $form->field($model, 'status')->dropDownList(UserHelper::getStatusArray()) ?>
            </div>

            <hr>

            <div class="modal-form__row">
                <?= $form->field($model, 'password', [
                    'template' => '{label}{input}{error}<span class="modal-form__input-link user-create-password-generator">Сгенерировать</span>'
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
        <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => 'btn btn--success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <i class="modal__close icon-close"></i>
</div>
<?php

$inputPassword = Html::getInputId($model, 'password');
$inputPasswordRepeat = Html::getInputId($model, 'passwordRepeat');

$js = <<<JS

$('.user-create-password-generator').click(function() {
    PasswordGenerator.generate(['#$inputPassword', '#$inputPasswordRepeat']) 
});

JS;

$this->registerJs($js);

?>