<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\search\CareSearch;
use app\core\helpers\CareHelper;
use app\core\helpers\UserHelper;

/** @var $searchModel CareSearch */

// Variables
$checked = $searchModel->my ? 'checked' : '';
$statuses = CareHelper::getStatuses();
$statuses['Расширенные'] = [
    CareSearch::STATUS_COMPLETED => 'Завершен',
    CareSearch::STATUS_HANDLE => 'В обработке'
];

?>
<div class="care-filter">
    <div class="care-filter__left">
        <div class="care-filter__buttons">
            <button type="submit" class="care-filter__button icon-refresh" onclick="Care.refresh()"></button>
            <a href="<?= Url::to(['/appeal/index']) ?>" class="care-filter__button icon-add js-view-modal"></a>
            <button type="button" class="care-filter__button care-filter__button--filter icon-filter_list<?= $searchModel->isFilterUsed() ? ' care-filter__button--active' : '' ?>" onclick="Care.filter()"></button>
        </div>
        <div class="care-filter__checkbox">
            <label class="care-filter__checkbox-label">
                <input class="care-filter__checkbox-input" id="filter-my" type="checkbox" <?= $checked; ?>>
                <span class="care-filter__checkbox-mark icon-check"></span>
                <?= Yii::t('app', 'My')?>
            </label>
        </div>
    </div>
    <div class="care-filter__right">
        <div class="care-filter__items">
            <div class="care-filter__item">
                <?= Html::activeDropDownList($searchModel, 'status', $statuses, [
                    'class' => 'care-filter__input care-filter__status',
                    'prompt' => 'Все статусы'
                ]) ?>
            </div>
            <div class="care-filter__item">
                <?= Html::activeTextInput($searchModel, 'date_range', [
                    'class' => 'care-filter__input care-filter__date',
                    'placeholder' => 'Период'
                ]) ?>
                <?= Html::activeHiddenInput($searchModel, 'id', [
                    'class' => 'care-filter__id'
                ])?>
                <?= Html::activeHiddenInput($searchModel, 'my', [
                    'class' => 'care-filter__my'
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
                    <label class="control-label"><?= $searchModel->getAttributeLabel('handler_id') ?></label>
                    <?= Html::dropDownList(null, $searchModel->handler_id, UserHelper::getSelectArray(), [
                        'prompt' => Yii::t('app', 'All'),
                        'data-input' => Html::getInputId($searchModel, 'handler_id')
                    ]) ?>
                    <?= Html::activeHiddenInput($searchModel, 'handler_id') ?>
                </div>
                <div class="form-group">
                    <label class="control-label"><?= $searchModel->getAttributeLabel('order_number') ?></label>
                    <?= Html::textInput(null, $searchModel->order_number, [
                        'data-input' => Html::getInputId($searchModel, 'order_number')
                    ]) ?>
                    <?= Html::activeHiddenInput($searchModel, 'order_number') ?>
                </div>
                <div class="form-group">
                    <label class="control-label">Был передан</label>
                    <?= Html::dropDownList(null, $searchModel->transferred, [1 => 'Да'], [
                        'prompt' => '',
                        'data-input' => Html::getInputId($searchModel, 'transferred')
                    ]) ?>
                    <?= Html::activeHiddenInput($searchModel, 'transferred') ?>
                </div>
            </div>
            <div class="modal-form__row modal-form__row--3">
                <div class="form-group">
                    <label class="control-label"><?= $searchModel->getAttributeLabel('language') ?></label>
                    <?= Html::dropDownList(null, $searchModel->language, CareHelper::getLanguages(), [
                        'prompt' => Yii::t('app', 'All'),
                        'data-input' => Html::getInputId($searchModel, 'language')
                    ]) ?>
                    <?= Html::activeHiddenInput($searchModel, 'language') ?>
                </div>
                <div class="form-group">
                    <label class="control-label"><?= $searchModel->getAttributeLabel('delivery_late') ?></label>
                    <?= Html::dropDownList(null, $searchModel->delivery_late, CareHelper::getDeliveryLateArray(), [
                        'prompt' => Yii::t('app', 'All'),
                        'data-input' => Html::getInputId($searchModel, 'delivery_late')
                    ]) ?>
                    <?= Html::activeHiddenInput($searchModel, 'delivery_late') ?>
                </div>
                <div class="form-group">
                    <label class="control-label"><?= $searchModel->getAttributeLabel('complaint_object') ?></label>
                    <?= Html::dropDownList(null, $searchModel->complaint_object, CareHelper::getComplaintObjectArray(), [
                        'prompt' => Yii::t('app', 'All'),
                        'data-input' => Html::getInputId($searchModel, 'complaint_object')
                    ]) ?>
                    <?= Html::activeHiddenInput($searchModel, 'complaint_object') ?>
                </div>
            </div>
        </div>
        <div class="modal__footer">
            <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
            <?= Html::button('Применить', ['class' => 'btn btn--success', 'onclick' => 'Care.filterSubmit()']) ?>
        </div>
        <i class="modal__close icon-close"></i>
    </div>
</div>