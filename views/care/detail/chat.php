<?php

use app\entities\Care;
use app\core\helpers\UserHelper;
use app\core\helpers\CareHelper;
use app\core\helpers\CareEventHelper;

/** @var $care Care */

?>
<?php foreach ($care->histories as $history) : $author = $history->createdBy; ?>
    <div class="care-chat__item">
        <?php if ($author->role == UserHelper::ROLE_BOT) : ?>
            <img src="/images/bot.png" alt="Bot" class="care-chat__bot">
        <?php else : ?>
            <i class="care-chat__user icon-person"></i>
        <?php endif; ?>
        <div class="care-chat__block">
            <div class="care-chat__author">
                <?= $author->full_name ?>
                <span class="care-chat__role">(<?= UserHelper::getRoleName($author->role) ?>)</span>
            </div>
            <div class="care-chat__status">
                <?= CareHelper::getStatusName($history->status_before) ?>
                <i class="care-chat__arrow icon-arrow_forward"></i>
                <?= CareHelper::getStatusName($history->status_after) ?>
            </div>
        </div>
        <div class="care-chat__date"><?= date('d.m.y H:i', $history->created_at) ?></div>
    </div>
    <?php foreach ($history->careEvents as $event): ?>
        <div class="care-chat__item care-chat__event">
            <div class="care-chat__block <?= CareEventHelper::getPriorityClass($event->type) ?>">
                <div class="care-chat__author"><?= $event->createdBy->full_name ?></div>
                <div class="care-chat__text">
                    <?php if ($icon = CareEventHelper::getIconClass($event->type)) : ?>
                        <i class="care-chat__icon <?= $icon ?>"></i>
                    <?php endif; ?>
                    <?php if ($event->type == CareEventHelper::TYPE_CARE_SOLUTION_TEXT) : ?>
                        <strong>Решение:</strong>
                        <br>
                    <?php endif; ?>
                    <?= CareEventHelper::format(CareEventHelper::getMessage($event)) ?>
                </div>
            </div>
            <div class="care-chat__date"><?= date('d.m.y H:i', $event->created_at) ?></div>
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>