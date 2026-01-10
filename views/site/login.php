<?php

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\modules\auth\forms\LoginStartForm */

use yii\helpers\Html;
use app\assets\AuthAsset;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

// Assets
AuthAsset::register($this);

$this->title = Yii::t('app', 'Login page');
?>
<div class="wrp">
    <div class="container">
        <div id="auth-content">
            <h2 class="login__title">Вход</h2>
            <div class="login__desc">Введите номер телефона, чтобы войти</div>
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
                'action' => ['login'],
                'validateOnChange' => false,
                'validateOnBlur' => false,
                'fieldConfig' => [
                    'options' => ['class' => 'login-form__group'],
                    'template' => "{input}\n{hint}",
                    'inputOptions' => ['class' => 'login-form__input']
                ],
            ]); ?>

            <?= $form
                ->field($model, 'phone')
                ->widget(MaskedInput::class, [
                    'mask' => '+7(999)999-99-99',
                    'options' => [
                        'class' => 'login-form__input',
                        'placeholder' => Yii::t('app', 'Phone'),
                        'autocomplete' => 'tel', // Лучше использовать 'tel' для мобильных
                        'autofocus' => true
                    ]
                ])->label(false) // Обычно label скрывают, если есть placeholder
            ?>
            <?= Html::submitButton('Войти', ['class' => 'login-form__btn']) ?>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
<?php
$js = <<<JS
$(document).on('submit', '#auth-content form', function(e) {
    e.preventDefault();
    
    var form = $(this);
    var btn = form.find('button[type="submit"]');
    
    // Блокируем кнопку
    btn.prop('disabled', true);

    $.ajax({
        url: form.attr('action'),
        type: 'POST',
        dataType: 'json',
        data: form.serialize(),
        success: function(response) {
            btn.prop('disabled', false);

            if (response.success) {
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else if (response.html) {
                    $('#auth-content').html(response.html);
                }
            } else {
                if (response.error) {
                    // Pass
                    var passInput = form.find('#password-input');
                    if (passInput.length) {
                        form.find('.field-password-input').addClass('has-error');
                    }

                    // Otp
                    var otpInputs = form.find('.otp-digit');
                    if (otpInputs.length) {
                        otpInputs.addClass('otp-error');
                        otpInputs.val(''); 
                        form.find('.real-otp-input').val('');
                        otpInputs.first().focus();
                    }
                }
            }
        }
    });
    
    return false;
});
JS;
$this->registerJs($js);
?>