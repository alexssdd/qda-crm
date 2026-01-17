<?php

use yii\helpers\Html;
use app\search\OrderSearch;
use app\core\helpers\DeliveryHelper;
use app\modules\order\helpers\OrderHelper;
use app\modules\order\helpers\PaymentHelper;

/** @var $searchModel OrderSearch */

// Variables
$checked = $searchModel->my ? 'checked' : '';
$statuses = OrderHelper::getStatuses();

?>
<div class="order-filter">
    <div class="order-filter__left">
        <div class="order-filter__buttons">
            <button type="button" class="order-filter__button icon-refresh" onclick="Order.refresh()"></button>
            <button type="button" class="order-filter__button icon-add" onclick="Order.create()"></button>
            <button type="button" class="order-filter__button order-filter__button--filter icon-filter_list<?= $searchModel->isFilterUsed() ? ' order-filter__button--active' : '' ?>" onclick="Order.filter()"></button>
        </div>
        <div class="order-filter__checkbox">
            <label class="order-filter__checkbox-label">
                <input class="order-filter__checkbox-input" id="filter-my" type="checkbox" <?= $checked; ?>>
                <span class="order-filter__checkbox-mark icon-check"></span>
                <?= Yii::t('app', 'My')?>
            </label>
        </div>
    </div>
    <div class="order-filter__right">
        <div class="order-filter__items">
            <div class="order-filter__item">
                <?= Html::activeDropDownList($searchModel, 'status', $statuses, [
                    'class' => 'order-filter__input order-filter__status',
                    'prompt' => 'Все статусы'
                ]) ?>
            </div>
            <div class="order-filter__item">
                <?= Html::activeTextInput($searchModel, 'date_range', [
                    'class' => 'order-filter__input order-filter__date',
                    'placeholder' => 'Период'
                ]) ?>
                <?= Html::activeHiddenInput($searchModel, 'id', [
                    'class' => 'order-filter__id'
                ])?>
                <?= Html::activeHiddenInput($searchModel, 'my', [
                    'class' => 'order-filter__my'
                ])?>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-filter modal-form" role="alert">
    <div class="modal__container modal__container--1000">
        <div class="modal__title">Фильтр</div>
        <div class="modal__body">
            <div class="modal-form__row modal-form__row--3">
                <div class="form-group">
                    <label class="control-label"><?= $searchModel->getAttributeLabel('delivery_method') ?></label>
                    <?= Html::dropDownList(null, $searchModel->delivery_method, DeliveryHelper::getMethods(), [
                        'prompt' => Yii::t('app', 'All'),
                        'data-input' => Html::getInputId($searchModel, 'delivery_method')
                    ]) ?>
                    <?= Html::activeHiddenInput($searchModel, 'delivery_method') ?>
                </div>
                <div class="form-group">
                    <label class="control-label"><?= $searchModel->getAttributeLabel('handler_id') ?></label>
                    <?= Html::dropDownList(null, $searchModel->handler_id, [], [
                        'prompt' => Yii::t('app', 'All'),
                        'data-input' => Html::getInputId($searchModel, 'handler_id')
                    ]) ?>
                    <?= Html::activeHiddenInput($searchModel, 'handler_id') ?>
                </div>
                <div class="form-group">
                    <label class="control-label"><?= $searchModel->getAttributeLabel('transferred') ?></label>
                    <?= Html::dropDownList(null, $searchModel->transferred, [1 => 'Да'], [
                        'prompt' => '',
                        'data-input' => Html::getInputId($searchModel, 'transferred')
                    ]) ?>
                    <?= Html::activeHiddenInput($searchModel, 'transferred') ?>
                </div>
            </div>
            <div class="modal-form__row modal-form__row--3">
                <div class="form-group">
                    <label class="control-label"><?= $searchModel->getAttributeLabel('payment_method') ?></label>
                    <?= Html::dropDownList(null, $searchModel->payment_method, PaymentHelper::getMethods(), [
                        'prompt' => Yii::t('app', 'All'),
                        'data-input' => Html::getInputId($searchModel, 'payment_method')
                    ]) ?>
                    <?= Html::activeHiddenInput($searchModel, 'payment_method') ?>
                </div>
                <div class="form-group">
                    <label class="control-label"><?= $searchModel->getAttributeLabel('vendor_number') ?></label>
                    <?= Html::textInput(null, $searchModel->vendor_number, [
                        'data-input' => Html::getInputId($searchModel, 'vendor_number')
                    ]) ?>
                    <?= Html::activeHiddenInput($searchModel, 'vendor_number') ?>
                </div>
                <div class="form-group">
                    <label class="control-label"><?= $searchModel->getAttributeLabel('vendor_id') ?></label>
                    <?= Html::textInput(null, $searchModel->vendor_id, [
                        'data-input' => Html::getInputId($searchModel, 'vendor_id')
                    ]) ?>
                    <?= Html::activeHiddenInput($searchModel, 'vendor_id') ?>
                </div>
            </div>
            <div class="modal-form__row modal-form__row--3">
                <div class="form-group">
                    <label class="control-label"><?= $searchModel->getAttributeLabel('event') ?></label>
                    <?= Html::dropDownList(null, $searchModel->event, [], [
                        'prompt' => Yii::t('app', 'All'),
                        'data-input' => Html::getInputId($searchModel, 'event')
                    ]) ?>
                    <?= Html::activeHiddenInput($searchModel, 'event') ?>
                </div>
            </div>
            <div class="modal-form__row modal-form__row--3">
            </div>
        </div>
        <div class="modal__footer">
            <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
            <?= Html::button('Применить', ['class' => 'btn btn--success', 'onclick' => 'Order.filterSubmit()']) ?>
        </div>
        <i class="modal__close icon-close"></i>
    </div>
</div>