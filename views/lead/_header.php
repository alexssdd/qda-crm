<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\search\LeadSearch;
use app\core\helpers\CityHelper;
use app\core\helpers\LeadHelper;
use app\core\helpers\BrandHelper;

/** @var $this View */
/** @var $searchModel LeadSearch */

?>
<div class="lead-filter">
    <div class="lead-filter__left">
        <div class="lead-filter__buttons">
            <button type="submit" class="lead-filter__button icon-refresh" onclick="Lead.refresh()"></button>
            <a href="<?= Url::to(['/lead/create']) ?>" class="lead-filter__button icon-add js-view-modal"></a>
            <button type="button" class="lead-filter__button icon-filter_list" onclick="Lead.filter()"></button>
        </div>
    </div>
    <div class="lead-filter__right">
        <div class="lead-filter__items">
            <div class="lead-filter__item">
                <?= Html::activeDropDownList($searchModel, 'city_id', CityHelper::getSelectArray(), [
                    'class' => 'lead-filter__input lead-filter__city',
                    'prompt' => 'Все города'
                ]) ?>
            </div>
            <div class="lead-filter__item">
                <?= Html::activeDropDownList($searchModel, 'brand_id', BrandHelper::getSelectArray(), [
                    'class' => 'lead-filter__input lead-filter__brand',
                    'prompt' => 'Все бренды'
                ]) ?>
            </div>
            <div class="lead-filter__item">
                <?= Html::activeDropDownList($searchModel, 'channel', LeadHelper::getChannelArray(), [
                    'class' => 'lead-filter__input lead-filter__channel',
                    'prompt' => 'Все каналы'
                ]) ?>
            </div>
            <div class="lead-filter__item">
                <?= Html::activeTextInput($searchModel, 'date_range', [
                    'class' => 'lead-filter__input lead-filter__date',
                    'placeholder' => 'Период'
                ]) ?>
            </div>
        </div>
    </div>
</div>
<div class="modal modal-filter modal-form" role="alert">
    <div class="modal__container modal__container--1000">
        <div class="modal__title">Фильтр</div>
        <div class="modal__body">
            <div class="modal-form__row">
                <div class="form-group">
                    <label class="control-label">Способ доставки</label>
                    <select class="modal-filter__input">
                        <option>Все</option>
                        <option value="1">Значение 1</option>
                        <option value="2">Значение 2</option>
                        <option value="3">Значение 3</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Способ оплаты</label>
                    <select class="modal-filter__input">
                        <option>Все</option>
                        <option value="1">Значение 1</option>
                        <option value="2">Значение 2</option>
                        <option value="3">Значение 3</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Статус оплаты</label>
                    <select class="modal-filter__input">
                        <option>Все</option>
                        <option value="1">Значение 1</option>
                        <option value="2">Значение 2</option>
                        <option value="3">Значение 3</option>
                    </select>
                </div>
            </div>
            <div class="modal-form__row">
                <div class="form-group">
                    <label class="control-label">Номер заказа партнера</label>
                    <input type="text" class="modal-filter__input">
                </div>
                <div class="form-group">
                    <label class="control-label">ИД заказа партнера</label>
                    <input type="text" class="modal-filter__input">
                </div>
                <div class="form-group">
                    <label class="control-label">Событие</label>
                    <select class="modal-filter__input">
                        <option>Все</option>
                        <option value="1">Значение 1</option>
                        <option value="2">Значение 2</option>
                        <option value="3">Значение 3</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal__footer">
            <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
            <?= Html::button('Применить', ['class' => 'btn btn--success']) ?>
        </div>
        <i class="modal__close icon-close"></i>
    </div>
</div>