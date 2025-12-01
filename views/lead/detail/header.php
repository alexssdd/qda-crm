<?php

use app\entities\Lead;
use app\core\helpers\LeadHelper;

/** @var $lead Lead */

?>
<div class="lead-header">
    <div class="lead-header__left">
        <div class="lead-header__items">
            <div class="lead-header__item">
                <span class="lead-header__label">Номер:</span>
                <span class="lead-header__value"><?= $lead->number ?></span>
            </div>
            <div class="lead-header__item">
                <span class="lead-header__label">Дата:</span>
                <span class="lead-header__value"><?= LeadHelper::getCreated($lead)  ?></span>
            </div>
            <div class="lead-header__item">
                <span class="lead-header__label">Канал:</span>
                <span class="lead-header__value"><?= LeadHelper::getChannelName($lead->channel) ?></span>
            </div>
            <div class="lead-header__item">
                <span class="lead-header__label">Оператор:</span>
                <span class="lead-header__value"><?= LeadHelper::getHandlerName($lead) ?></span>
            </div>
        </div>
    </div>
    <div class="lead-header__right">
        <div class="lead-header__items">
            <div class="lead-header__item">
                <span class="lead-header__label">Внешний номер:</span>
                <span class="lead-header__value"><?= $lead->vendor_number ?></span>
            </div>
        </div>
        <div class="lead-header__time"></div>
    </div>
</div>
<?php

$seconds = time() - $lead->created_at;
$stop = 'false';

if (LeadHelper::isCompleted($lead->status)) {
    $stop = true;
    $seconds = $lead->completed_at - $lead->created_at;
}

$js = <<<JS

Lead.id = $lead->id;
Lead.initTime($seconds, $stop);

JS;

$this->registerJs($js);

?>
