<?php

use app\entities\Lead;
use app\core\helpers\TextHelper;
use app\core\helpers\UserHelper;
use app\core\helpers\LeadHelper;
use app\core\helpers\LeadEventHelper;

/** @var $lead Lead */

?>
<?php foreach ($lead->histories as $history) : $author = $history->createdBy; ?>
    <div class="lead-chat__item">
        <?php if ($author->role == UserHelper::ROLE_BOT) : ?>
            <img src="/images/bot.png" alt="Bot" class="lead-chat__bot">
        <?php else : ?>
            <i class="lead-chat__user icon-person"></i>
        <?php endif; ?>
        <div class="lead-chat__block">
            <div class="lead-chat__author">
                <?= $author->full_name ?>
                <span class="lead-chat__role">(<?= UserHelper::getRoleName($author->role) ?>)</span>
            </div>
            <div class="lead-chat__status">
                <?= LeadHelper::getStatusName($history->status_before) ?>
                <i class="lead-chat__arrow icon-arrow_forward"></i>
                <?= LeadHelper::getStatusName($history->status_after) ?>
            </div>
        </div>
        <div class="lead-chat__date"><?= date('d.m.y H:i', $history->created_at) ?></div>
    </div>
    <?php foreach ($history->leadEvents as $event): ?>
        <div class="lead-chat__item lead-chat__event">
            <div class="lead-chat__block <?= LeadEventHelper::getPriorityClass($event->type) ?>">
                <div class="lead-chat__author"><?= $event->createdBy->full_name ?></div>
                <div class="lead-chat__text">
                    <?php if ($icon = LeadEventHelper::getIconClass($event->type)) : ?>
                        <i class="lead-chat__icon <?= $icon ?>"></i>
                    <?php endif; ?>
                    <?= TextHelper::getLeadMessage($event)?>
                </div>
            </div>
            <div class="lead-chat__date"><?= date('d.m.y H:i', $event->created_at) ?></div>
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>