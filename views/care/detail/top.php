<?php

use yii\web\View;
use app\entities\Care;
use app\core\helpers\CareHelper;

/* @var $care Care */
/* @var $this View */
?>
<div class="care-top">
    <div class="care-info">
        <div class="care-info__row">
            <div class="care-info__item">
                <div class="care-info__label"><?= $care->getAttributeLabel('language') ?></div>
                <input class="care-info__input" type="text" readonly value="<?= CareHelper::getLanguage($care->language) ?>">
            </div>
            <div class="care-info__item">
                <div class="care-info__label"><?= $care->getAttributeLabel('channel') ?></div>
                <input class="care-info__input" type="text" readonly value="<?= CareHelper::getChannelName($care->channel) ?>">
            </div>
        </div>
        <div class="care-info__row">
            <div class="care-info__item">
                <div class="care-info__label"><?= $care->getAttributeLabel('count_request') ?></div>
                <input class="care-info__input" type="text" readonly value="<?= CareHelper::getCountRequest($care->count_request) ?>">
            </div>
            <div class="care-info__item">
                <div class="care-info__label"><?= $care->getAttributeLabel('count_problem') ?></div>
                <input class="care-info__input" type="text" readonly value="<?= CareHelper::getCountProblem($care->count_problem) ?>">
            </div>
        </div>
    </div>
</div>