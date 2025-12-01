<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use app\core\helpers\CityHelper;
use app\core\helpers\CareHelper;
use conquer\select2\Select2Widget;
use app\forms\appeal\AppealCreateForm;

/* @var $this View */
/* @var $products string */
/* @var $model AppealCreateForm */

?>
<div class="modal__container modal__container--1000">
    <div class="modal__title">Новое обращение</div>
    <?php $form = ActiveForm::begin([
        'id' => 'appeal-form',
        'fieldConfig' => [
            'template' => "{input}\n{hint}"
        ]
    ]) ?>
    <div class="modal__body">
        <div class="appeal">
            <div class="appeal-header">
                <div class="appeal-header__item">
                    <?= $form->field($model, 'channel')->dropDownList(CareHelper::getChannelArray(), [
                        'prompt' => Yii::t('appeal', 'Select channel')
                    ]) ?>
                </div>
                <div class="appeal-header__item">
                    <?= $form->field($model, 'phone')->widget(MaskedInput::class, [
                        'mask' => '+7(999)999-99-99',
                        'options' => [
                            'class' => 'form-control',
                            'autocomplete' => 'off',
                            'placeholder' => $model->getAttributeLabel('phone')
                        ]
                    ]) ?>
                </div>
                <div class="appeal-header__item">
                    <?= $form->field($model, 'name')->textInput([
                        'maxlength' => true,
                        'placeholder' => $model->getAttributeLabel('name')
                    ]) ?>
                </div>
                <div class="appeal-header__item">
                    <?= $form->field($model, 'city_id')->widget(Select2Widget::class, [
                        'items' => CityHelper::getSelectArray(),
                        'bootstrap' => false
                    ]) ?>
                </div>
            </div>
            <div class="appeal-info">
                <div class="appeal-info__item">
                    <div class="appeal-info__label"><?= $model->getAttributeLabel('text') ?></div>
                    <?= $form->field($model, 'text')->textarea([
                        'class' => 'appeal-info__textarea',
                        'placeholder' => 'Подробно опишите обращение'
                    ]) ?>
                </div>
            </div>
            <div class="appeal-actions">
                <button type="button" class="appeal-actions__button btn btn--default" disabled onclick="Appeal.actionCustomerCares()">Обращения клиента</button>
                <button type="button" class="appeal-actions__button btn btn--default" disabled onclick="Appeal.actionCustomerOrders()">Заказы клиента</button>
            </div>
            <div class="appeal-body">
                <div class="appeal-body__left">
                    <div class="appeal-form">
                        <div class="appeal-form__items">
                            <div class="appeal-form__item required">
                                <label class="appeal-form__label"><?= $model->getAttributeLabel('type') ?></label>
                                <div class="appeal-form__block">
                                    <?= $form->field($model, 'type')->dropDownList(CareHelper::getTypeArray()) ?>
                                </div>
                            </div>
                            <div class="appeal-form__item required">
                                <label class="appeal-form__label"><?= $model->getAttributeLabel('order_number') ?></label>
                                <div class="appeal-form__block">
                                    <?= $form->field($model, 'order_number')->textInput([
                                        'maxlength' => true,
                                        'placeholder' => 'Если нет, то скопируйте и поставьте цифру 777'
                                    ]) ?>
                                </div>
                            </div>
                            <div class="appeal-form__item">
                                <label class="appeal-form__label"><?= $model->getAttributeLabel('rating') ?></label>
                                <div class="appeal-form__block">
                                    <?= $form->field($model, 'rating')->textInput([
                                        'maxlength' => true
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                        <div class="appeal-form__hidden">
                            <?= $form->field($model, 'customer_id')->hiddenInput() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal__footer modal__footer--bordered">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
        <?= Html::submitButton('Создать', ['class' => 'btn btn--success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    <i class="modal__close icon-close"></i>
</div>
<?php

$js = <<<JS

Appeal.init();

JS;

$this->registerJs($js);

?>