<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\entities\Care;
use app\core\rules\CareRules;
use app\core\helpers\CareHelper;
use app\core\helpers\PhoneHelper;

/** @var $this View */
/** @var $care Care */

// Variables
$statuses = CareHelper::getAvailableStatuses($care);
$statuses[$care->status] = CareHelper::getStatusName($care->status);
$finished = CareHelper::isFinished($care->status);
$isPositive = $care->type == CareHelper::TYPE_POSITIVE;
$showColumns = $isPositive ? [] : ['complaint_reason', 'delivery_late', 'complaint_validity', 'compensation', 'final_status'];
if ($isPositive){
    $requiredFields = ['category', 'solution_measures'];
} else {
    switch ($care->channel){
        case CareHelper::CHANNEL_ASSISTANT:
        case CareHelper::CHANNEL_WHATSAPP:
        case CareHelper::CHANNEL_TELEGRAM:
        case CareHelper::CHANNEL_CHAT_SITE:
        case CareHelper::CHANNEL_REVIEW_TWO_GIS:
        case CareHelper::CHANNEL_KASPI_SERVICE_DESK:
            $requiredFields = ['compensation', 'category', 'complaint_reason', 'solution_measures', 'complaint_validity'];
            break;
        default:
            $requiredFields = ['compensation'];
            break;
    }
}

?>
<?= $form = Html::beginForm(['care/update', 'id' => $care->id])?>
<div class="care-body">
    <div class="care-body__items">
        <div class="care-body__item">
            <label class="care-body__label">Клиент</label>
            <div class="care-body__block">
                <?= Html::textInput(null, $care->name, ['class' => 'care-body__input', 'readonly' => true])?>
                <?php if ($care->customer_id) : ?>
                <a href="<?= Url::to(['/customer/detail', 'id' => $care->customer_id]) ?>" class="care-body__input-icon js-view-modal icon-person"></a>
                <?php endif; ?>
            </div>
        </div>
        <div class="care-body__item">
            <label class="care-body__label">Телефон</label>
            <div class="care-body__block">
                <?= Html::textInput(null, PhoneHelper::getMaskPhone($care->phone), ['class' => 'care-body__input', 'readonly' => true])?>
                <a href="tel:+<?= $care->phone ?>" class="care-body__input-icon care-body__input-icon--blue icon-call"></a>
            </div>
        </div>
        <div class="care-body__item">
            <label class="care-body__label"><?= $care->getAttributeLabel('order_number') ?></label>
            <div class="care-body__block">
                <?= Html::textInput('order_number', $care->order_number, ['class' => 'care-body__input'])?>
            </div>
        </div>
        <div class="care-body__item<?= in_array('category', $requiredFields) ? ' required' : '' ?>">
            <label class="care-body__label"><?= $care->getAttributeLabel('category') ?></label>
            <div class="care-body__block">
                <?= Html::dropDownList('category', $care->category, CareHelper::getCategoryArray(), [
                    'class' => 'care-body__input',
                    'prompt' => Yii::t('app', 'Select value'),
                    'id' => Html::getInputId($care, 'category'),
                    'required' => in_array('category', $requiredFields)
                ])?>
            </div>
        </div>
        <div class="care-body__item<?= in_array('complaint_reason', $requiredFields) ? ' required' : '' ?>" style="display: <?= in_array('complaint_reason', $showColumns) ? 'flex' : 'none' ?>">
            <label class="care-body__label"><?= $care->getAttributeLabel('complaint_reason') ?></label>
            <div class="care-body__block">
                <?= Html::dropDownList('complaint_reason', $care->complaint_reason, [], [
                    'class' => 'care-body__input',
                    'prompt' => Yii::t('app', 'Select value'),
                    'id' => Html::getInputId($care, 'complaint_reason'),
                    'required' => in_array('complaint_reason', $requiredFields)
                ])?>
            </div>
        </div>
        <div class="care-body__item care-body__item--store_number">
            <label class="care-body__label"><?= $care->getAttributeLabel('store_number') ?></label>
            <div class="care-body__block">
                <?= Html::textInput('store_number', $care->store_number, [
                    'class' => 'care-body__input',
                    'id' => Html::getInputId($care, 'store_number'),
                ])?>
            </div>
        </div>
        <div class="care-body__item care-body__item--delivery_late" style="display: <?= in_array('delivery_late', $showColumns) ? 'flex' : 'none' ?>">
            <label class="care-body__label"><?= $care->getAttributeLabel('delivery_late') ?></label>
            <div class="care-body__block">
                <?= Html::dropDownList('delivery_late', $care->delivery_late, CareHelper::getDeliveryLateArray(), [
                    'class' => 'care-body__input',
                    'prompt' => Yii::t('app', 'Select value')
                ])?>
            </div>
        </div>
        <div class="care-body__item<?= in_array('solution_measures', $requiredFields) ? ' required' : '' ?>">
            <label class="care-body__label"><?= $care->getAttributeLabel('solution_measures') ?></label>
            <div class="care-body__block">
                <?= Html::dropDownList('solution_measures', $care->solution_measures, [], [
                    'class' => 'care-body__input',
                    'prompt' => Yii::t('app', 'Select value'),
                    'id' => Html::getInputId($care, 'solution_measures'),
                    'required' => in_array('solution_measures', $requiredFields)
                ])?>
            </div>
        </div>
        <div class="care-body__item<?= in_array('compensation', $requiredFields) ? ' required' : '' ?>" style="display: <?= in_array('compensation', $showColumns) ? 'flex' : 'none' ?>">
            <label class="care-body__label"><?= $care->getAttributeLabel('compensation') ?></label>
            <div class="care-body__block">
                <?= Html::dropDownList('compensation', $care->compensation, CareHelper::getCompensationArray(), [
                    'class' => 'care-body__input',
                    'prompt' => Yii::t('app', 'Select value'),
                    'id' => Html::getInputId($care, 'compensation'),
                    'required' => in_array('compensation', $requiredFields)
                ])?>
            </div>
        </div>
        <div class="care-body__item">
            <label class="care-body__label"><?= $care->getAttributeLabel('complaint_personal') ?></label>
            <div class="care-body__block">
                <?= Html::textInput('complaint_personal', $care->complaint_personal, ['class' => 'care-body__input'])?>
            </div>
        </div>
        <div class="care-body__item<?= in_array('complaint_validity', $requiredFields) ? ' required' : '' ?>" style="display: <?= in_array('complaint_validity', $showColumns) ? 'flex' : 'none' ?>">
            <label class="care-body__label"><?= $care->getAttributeLabel('complaint_validity') ?></label>
            <div class="care-body__block">
                <?= Html::dropDownList('complaint_validity', $care->complaint_validity, CareHelper::getComplaintValidityArray(), [
                    'class' => 'care-body__input',
                    'prompt' => Yii::t('app', 'Select value'),
                    'required' => in_array('complaint_validity', $requiredFields)
                ])?>
            </div>
        </div>
        <div class="care-body__item" style="display: <?= in_array('final_status', $showColumns) ? 'flex' : 'none' ?>">
            <label class="care-body__label"><?= $care->getAttributeLabel('final_status') ?></label>
            <div class="care-body__block">
                <?= Html::dropDownList('final_status', $care->final_status, CareHelper::getFinalStatusArray(), [
                    'class' => 'care-body__input',
                    'prompt' => Yii::t('app', 'Select value'),
                ])?>
            </div>
        </div>
        <div class="care-body__item">
            <label class="care-body__label"><?= $care->getAttributeLabel('status') ?></label>
            <div class="care-body__block">
                <?= Html::dropDownList('status', $care->status, $statuses, ['class' => 'care-body__input'])?>
            </div>
        </div>
    </div>
    <div class="care-footer">
        <div class="care-actions">
            <div class="care-actions__heading">Дополнительные действия</div>
            <div class="care-actions__list">
                <?php if (!$finished) : ?>
                <div class="care-actions__item">
                    <a href="<?= Url::to(['/care/transfer', 'id' => $care->id]) ?>" class="care-actions__link js-view-modal">Передать обращение</a>
                </div>
                <div class="care-actions__item">
                    <a href="<?= Url::to(['/care/solution', 'id' => $care->id]) ?>" class="care-actions__link js-view-modal">Подробно описать решение</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="care-footer__left">
            <button class="btn btn--default" type="button" onclick="Care.actions()">Действия</button>
        </div>
        <div class="care-footer__right">
            <?php if (CareRules::canSave($care->status)): ?>
                <button type="submit" class="btn btn--success care-body__button">Сохранить</button>
            <?php endif;?>
        </div>
    </div>
</div>
<?= Html::endForm(); ?>
<?php

$isPositiveJs = $isPositive ? 'true' : 'false';

$js = <<<JS

Care.isPositive = $isPositiveJs;
Care.valueComplaintReason = '$care->complaint_reason';
Care.valueSolutionMeasures = '$care->solution_measures';
Care.initModel();

JS;

$this->registerJs($js);

?>