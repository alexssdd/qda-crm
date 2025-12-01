<?php

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\forms\LoginForm */

use yii\helpers\Html;
use app\assets\AuthAsset;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;

// Assets
AuthAsset::register($this);

// View params
$this->title = Yii::t('app', 'Login page')
?>
<div class="wrp">
    <div class="container">
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
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
                    'autocomplete' => 'off'
                ]
            ])
        ?>
        <?= $form->field($model, 'password')->passwordInput(['placeholder' => Yii::t('app', 'Password')]) ?>
        <?= Html::submitButton('Войти', ['class' => 'login-form__btn']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
