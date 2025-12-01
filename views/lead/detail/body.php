<?php

use yii\helpers\Url;
use yii\helpers\Html;
use app\entities\Lead;
use app\core\helpers\CityHelper;
use app\core\helpers\LeadHelper;
use app\core\helpers\PhoneHelper;

/** @var $lead Lead */

?>
<?= $form = Html::beginForm(['lead/update', 'id' => $lead->id])?>
<div class="lead-body">
    <div class="lead-body__items">
        <div class="lead-body__item">
            <label class="lead-body__label">Клиент</label>
            <div class="lead-body__block">
                <?= Html::textInput(null, $lead->name, ['class' => 'lead-body__input', 'readonly' => true]) ?>
                <?php if ($lead->customer_id) : ?>
                <a href="<?= Url::to(['/customer/detail', 'id' => $lead->customer_id]) ?>" class="lead-body__input-icon js-view-modal icon-person"></a>
                <?php endif; ?>
            </div>
        </div>
        <div class="lead-body__item">
            <label class="lead-body__label">Телефон</label>
            <div class="lead-body__block">
                <?= Html::textInput(null, PhoneHelper::getMaskPhone($lead->phone), ['class' => 'lead-body__input', 'readonly' => true])?>
            </div>
        </div>
        <div class="lead-body__item">
            <label class="lead-body__label">Город</label>
            <div class="lead-body__block">
                <?= Html::dropDownList('city_id', $lead->city_id, CityHelper::getSelectArray(), [
                    'class' => 'lead-body__input"',
                    'prompt' => ''
                ])?>
            </div>
        </div>
        <div class="lead-body__item">
            <label class="lead-body__label"><?= $lead->getAttributeLabel('brand_id') ?></label>
            <div class="lead-body__block">
                <?= Html::textInput(null, $lead->brand ? $lead->brand->name : null, ['class' => 'lead-body__input', 'readonly' => true])?>
            </div>
        </div>
        <div class="lead-body__item">
            <label class="lead-body__label"><?= $lead->getAttributeLabel('title') ?></label>
            <div class="lead-body__block">
                <?= Html::textInput('title', $lead->title, ['class' => 'lead-body__input'])?>
            </div>
        </div>
        <div class="lead-body__item">
            <label class="lead-body__label">Статус</label>
            <div class="lead-body__block">
                <?= Html::dropDownList('status', $lead->status, LeadHelper::getAvailableStatusArray(), ['class' => 'lead-body__input"'])?>
            </div>
        </div>
    </div>
    <div class="lead-footer">
        <div class="lead-actions">
            <div class="lead-actions__heading">Дополнительные действия</div>
            <div class="lead-actions__list">
                <div class="lead-actions__item">
                    <a href="<?= Url::to(['/lead/transfer', 'id' => $lead->id]) ?>" class="lead-actions__link js-view-modal">Передать лид</a>
                </div>
            </div>
        </div>
        <div class="lead-footer__left">
            <button class="btn btn--default" type="button" onclick="Lead.actions()">Действия</button>
        </div>
        <div class="lead-footer__right">
            <button type="submit" class="btn btn--success lead-body__button">Сохранить</button>
        </div>
    </div>
</div>
<?= Html::endForm(); ?>
