<?php

use yii\web\View;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use app\forms\AddressSelectForm;
use app\core\helpers\AddressSelectHelper;

/** @var $this View */
/** @var $action string */
/** @var $doneCallback string */
/** @var $model AddressSelectForm */

$customerAddresses = $model->getCustomerAddresses();

?>
<div class="modal__container modal__container--1200 address-select">
    <div class="modal__title">Редактирование адреса</div>
    <?php $form = ActiveForm::begin([
        'id' => 'addressSelectForm',
        'action' => $action,
        'fieldConfig' => [
            'template' => "{label}\n<div class='address-select-form__input_wrp'>{input}</div>",
            'options' => ['class' => 'address-select-form__group'],
            'labelOptions' => ['class' => 'address-select-form__label']
        ]
    ])?>
    <div class="modal__body">
        <div class="tab-checkbox">
            <label class="tab-checkbox__item tab-checkbox__item--<?= AddressSelectHelper::TYPE_MAP ?>">
                <input<?= $model->type == AddressSelectHelper::TYPE_MAP ? ' checked' : '' ?> type="radio" class="tab-checkbox__input" name="<?= Html::getInputName($model, 'type') ?>" value="<?= AddressSelectHelper::TYPE_MAP ?>">
                <span class="tab-checkbox__name">Указать на карте</span>
            </label>
            <label class="tab-checkbox__item tab-checkbox__item--<?= AddressSelectHelper::TYPE_INPUT ?>">
                <input<?= !$model->type || $model->type == AddressSelectHelper::TYPE_INPUT ? ' checked' : '' ?> type="radio" class="tab-checkbox__input" name="<?= Html::getInputName($model, 'type') ?>" value="<?= AddressSelectHelper::TYPE_INPUT ?>">
                <span class="tab-checkbox__name">Ввести вручную</span>
            </label>
            <?php if ($customerAddresses) : ?>
                <label class="tab-checkbox__item tab-checkbox__item--<?= AddressSelectHelper::TYPE_LIST ?>">
                    <input<?= $model->type == AddressSelectHelper::TYPE_LIST ? ' checked' : '' ?> type="radio" class="tab-checkbox__input" name="<?= Html::getInputName($model, 'type') ?>" value="<?= AddressSelectHelper::TYPE_LIST ?>">
                    <span class="tab-checkbox__name">Выбрать из списка</span>
                </label>
            <?php endif; ?>
        </div>
        <div class="address-select__inputs">
            <?= $form->field($model, 'address', [
                'options' => [
                    'style' => 'min-width: 510px'
                ]
            ]) ?>
            <?= $form->field($model, 'house') ?>
            <?= $form->field($model, 'apartment') ?>
            <?= $form->field($model, 'intercom') ?>
            <?= $form->field($model, 'entrance') ?>
            <?= $form->field($model, 'floor') ?>
        </div>
        <?php if ($customerAddresses) : ?>
            <div class="customer-addresses">
                <label class="address-select-form__label">Список адресов</label>
                <div class="checkbox-list">
                    <?php foreach ($customerAddresses as $customerAddress) : ?>
                        <label class="checkbox-list__item<?= !$customerAddress['lat'] ? ' checkbox-list__item--disabled' : '' ?>">
                            <?php if ($customerAddress['title']): ?>
                                <span class="checkbox-list__name"><?= Html::encode($customerAddress['title']) ?> <span style="color: grey; font-size: 12px">(<?= AddressSelectHelper::getText($customerAddress) ?>)</span></span>
                            <?php else : ?>
                                <span class="checkbox-list__name"><?= AddressSelectHelper::getText($customerAddress) ?></span>
                            <?php endif; ?>
                            <?php if ($customerAddress['lat']) : ?>
                                <?= Html::radio('order-address-select-list', $customerAddress['is_default'], [
                                    'class' => 'checkbox-list__input',
                                    'data-title' => Html::encode($customerAddress['title']),
                                    'data-address' => Html::encode($customerAddress['address']),
                                    'data-lat' => Html::encode($customerAddress['lat']),
                                    'data-lng' => Html::encode($customerAddress['lng']),
                                    'data-house' => Html::encode($customerAddress['house']),
                                    'data-apartment' => Html::encode($customerAddress['apartment']),
                                    'data-intercom' => Html::encode($customerAddress['intercom']),
                                    'data-entrance' => Html::encode($customerAddress['entrance']),
                                    'data-floor' => Html::encode($customerAddress['floor']),
                                ]) ?>
                                <span class="checkbox-list__icon"></span>
                            <?php else : ?>
                                <input class="checkbox-list__input" type="radio" name="choose-address" disabled>
                                <span class="checkbox-list__badge">Нету нужных данных</span>
                                <span class="checkbox-list__icon"></span>
                            <?php endif; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="address-select__map" id="addressSelectMap"></div>
        <div class="d-none">
            <?= $form->field($model, 'lat')->hiddenInput() ?>
            <?= $form->field($model, 'lng')->hiddenInput() ?>
            <?= $form->field($model, 'title')->hiddenInput() ?>
        </div>
    </div>
    <div class="modal__footer">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.closeAdditional()"><?= Yii::t('app', 'Close'); ?></a>
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn--success address-select__button']) ?>
    </div>
    <i class="modal__close icon-close"></i>
    <?php ActiveForm::end() ?>
</div>
<?php

$city = $model->getCity();

$params = Json::encode([
    'cityLat' => $city->getLat() ?: 0,
    'cityLng' => $city->getLng() ?: 0,
    'latValue' => $model->lat ?: 0,
    'lngValue' => $model->lng ?: 0,
    'cityId' => $city->id,
    'cityName' => $city->name,
    'type' => $model->type ?: AddressSelectHelper::TYPE_INPUT,
    'doneCallback' => new JsExpression($doneCallback)
]);

$js = <<<JS

AddressSelect.init($params);

JS;

$this->registerJs($js);

?>