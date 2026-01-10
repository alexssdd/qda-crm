<?php

use app\core\helpers\PhoneHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\auth\forms\LoginPasswordForm $model */
/** @var string $phone */
?>

<div class="login-password-wrapper">
    <h2 class="login__title">С возвращением!</h2>
    <div class="login__desc">
        Вы входите как <strong><?= Html::encode(PhoneHelper::getMaskPhone($phone)) ?></strong>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'login-password-form',
        'action' => ['login-password'],
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validateOnSubmit' => true,
        'fieldConfig' => [
            'options' => ['class' => 'login-form__group'],
            'template' => "{input}\n{hint}",
            'inputOptions' => ['class' => 'login-form__input']
        ]
    ]); ?>

    <?= $form->field($model, 'phone')->hiddenInput(['value' => $phone])->label(false) ?>

    <div style="position: relative;">
        <?= $form->field($model, 'password')->passwordInput([
            'placeholder' => 'Введите ваш пароль',
            'autocomplete' => 'current-password',
            'id' => 'password-input', // ID для JS
            'style' => 'padding-right: 40px;' // Отступ для иконки глаза
        ])->label(false) ?>
        <div id="toggle-password" style="position: absolute; right: 15px; top: 13px; cursor: pointer; color: #666;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        </div>
    </div>

    <?= Html::submitButton('Войти в аккаунт', ['class' => 'login-form__btn']) ?>
    <div class="login__footer">
        <a href="/login" style="color: #666; text-decoration: none;">← Другой номер</a>
        <a href="<?= Url::to(['site/request-password-reset']) ?>" style="color: #666; text-decoration: none;">Забыли пароль?</a>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$script = <<<JS
(function() {
    const passInput = $('#password-input');
    const toggleBtn = $('#toggle-password');
    const form = $('#login-password-form');
    const errorBlock = form.find('.password-error');
    
    setTimeout(() => {
        passInput.focus();
    }, 150);
    
    toggleBtn.on('click', function() {
        const type = passInput.attr('type') === 'password' ? 'text' : 'password';
        passInput.attr('type', type);
        
        // Меняем цвет иконки, чтобы показать активность
        $(this).css('color', type === 'text' ? '#3b82f6' : '#666');
    });
    
    passInput.on('input', function() {
        form.find('.field-password-input').removeClass('has-error'); 
    });
})();
JS;
$this->registerJs($script);
?>