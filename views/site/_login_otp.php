<?php

use app\core\helpers\PhoneHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\modules\auth\forms\LoginOtpForm $model */
/** @var string $phone */
?>

<div class="login-otp-wrapper">
    <h2 class="login__title">Введите код</h2>
    <div class="login__desc">
        Мы отправили код подтверждения в мессенджер WhatsApp на номер <strong><?= Html::encode(PhoneHelper::getMaskPhone($phone)) ?></strong>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'login-otp-form',
        'action' => ['login-otp'],
        'enableClientValidation' => false,
    ]); ?>

    <?= $form->field($model, 'code', [
        'options' => ['class' => 'real-otp-input'],
        'template' => "{input}"
    ])->hiddenInput(['id' => 'hidden-otp-value'])->label(false) ?>

    <div class="otp-container" id="otp-inputs">
        <?php for($i=0; $i<6; $i++): ?>
            <input type="tel" maxlength="1" class="otp-digit" autocomplete="off">
        <?php endfor; ?>
    </div>

    <?= $form->field($model, 'phone')->hiddenInput(['value' => $phone])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Войти в аккаунт', ['class' => 'login-form__btn']) ?>
    </div>

    <div class="login__footer">
        <a href="/login" style="color: #666; text-decoration: none;">← Другой номер</a>
        <a href="#" class="resend-link" style="color: #666; text-decoration: none;">Отправить снова</a>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$script = <<<JS
(function() {
    const container = document.getElementById('otp-inputs');
    if (!container) return;

    const inputs = container.querySelectorAll('.otp-digit');
    const hiddenInput = document.getElementById('hidden-otp-value');
    const form = $('#login-otp-form');
    // const errorBlock удален

    function updateHiddenInput() {
        let code = '';
        inputs.forEach(input => code += input.value);
        hiddenInput.value = code;
    }
    
    function showError() {
        inputs.forEach(el => el.classList.add('otp-error'));
        inputs[0].focus();
    }

    // --- 1. ЛОГИКА ВВОДА ---
    inputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            // Сброс ошибки при вводе
            inputs.forEach(el => el.classList.remove('otp-error'));
            // errorBlock.text('') удален

            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            const val = e.target.value;

            if (val.length === 1) {
                updateHiddenInput();
                if (index < inputs.length - 1) inputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value) {
                if (index > 0) inputs[index - 1].focus();
            }
        });

        input.addEventListener('paste', (e) => {
            e.preventDefault();
            inputs.forEach(el => el.classList.remove('otp-error'));
            
            const pasteData = (e.clipboardData || window.clipboardData).getData('text');
            const digits = pasteData.replace(/[^0-9]/g, '').split('');

            if (digits.length > 0) {
                inputs.forEach((inp, i) => { if (digits[i]) inp.value = digits[i]; });
                updateHiddenInput();
                const lastIdx = Math.min(digits.length, inputs.length) - 1;
                if (lastIdx >= 0) inputs[lastIdx].focus();
                
                if (digits.length >= 6) form.submit(); 
            }
        });
    });
    
    setTimeout(() => inputs[0].focus(), 100);

    // --- 2. CLIENT-SIDE VALIDATION ---
    form.on('submit', function(e) {
        const code = hiddenInput.value;
        if (!code || code.length < 6) {
            e.preventDefault();
            e.stopPropagation();
            showError(); // Просто красим рамки, без текста
            return false;
        }
    });

})();
JS;
$this->registerJs($script);
?>