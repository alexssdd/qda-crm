<?php

use app\core\helpers\LeadHelper;

/** @var $result [] */

?>
<div class="lead-columns">
    <?php foreach ($result as $status => $items) : ?>
    <div class="lead-columns__column lead-columns__column--<?= LeadHelper::getStatusKey($status) ?>">
        <div class="lead-columns__heading">
            <?= LeadHelper::getStatusName($status) ?>
            <span class="lead-columns__count">(<?= count($items) ?>)</span>
        </div>
        <div class="lead-columns__items">
            <?php foreach ($items as $item) : ?>
            <div class="lead-item transition" data-id="<?= $item['id'] ?>" onclick="Lead.open(<?= $item['id'] ?>)">
                <div class="lead-item__title transition"><?= $item['title'] ?></div>
                <div class="lead-item__channel lead-item__channel--<?= LeadHelper::getChannelKey($item['channel']) ?>"><?= LeadHelper::getChannelName($item['channel']) ?></div>
                <div class="lead-item__date"><?= Yii::$app->formatter->asDatetime($item['created_at'], 'php:d M H:i') ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>