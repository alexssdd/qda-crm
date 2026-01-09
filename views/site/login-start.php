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
                'action' => ['login'], // Явно указываем экшен (или validate-phone, если разделили)
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
    var originalText = btn.text();
    
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
                // 1. УСПЕХ
                if (response.redirect) {
                    window.location.href = response.redirect;
                } else if (response.html) {
                    $('#auth-content').html(response.html);
                }
            } else {
                if (response.error) {
                    var errorBlock = form.find('.text-danger');
                    var otpInputs = form.find('.otp-digit');

                    // Логика: Если есть блок ошибки - пишем туда.
                    // Если блока нет, но есть OTP-поля - молчим (будет только визуальный эффект).
                    // Если нет ни того, ни другого - алерт (фоллбэк).
                    
                    if (errorBlock.length) {
                        errorBlock.text(response.error).show();
                    } else if (otpInputs.length === 0) {
                         // Показываем алерт только если это НЕ форма OTP (где мы удалили текст)
                        alert(response.error);
                    }

                    // Красим квадратики (визуализация ошибки)
                    if (otpInputs.length) {
                        otpInputs.addClass('otp-error');
                        otpInputs.val(''); 
                        form.find('.real-otp-input').val('');
                        otpInputs.first().focus();
                    }
                }
                // Б) Если пришли ошибки валидации (старый формат, на всякий случай)
                else if (response.errors) {
                    form.yiiActiveForm('updateMessages', response.errors, true);
                }
            }
        },
        error: function(jqXHR) {
            btn.prop('disabled', false);
            // Обработка редиректа сессии (если вдруг 302 проскочил как 200 HTML)
            if (jqXHR.status === 200 && jqXHR.responseText.indexOf('<!DOCTYPE html>') !== -1) {
                window.location.reload();
                return;
            }
            alert('Ошибка сервера: ' + jqXHR.statusText);
        }
    });
    
    return false;
});
JS;
$this->registerJs($js);
?>