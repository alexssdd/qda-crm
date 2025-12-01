<?php

use app\entities\Order;
use app\core\helpers\TextHelper;
use app\core\helpers\UserHelper;
use app\core\helpers\OrderHelper;
use app\core\helpers\OrderEventHelper;

/** @var $order Order */

?>
<?php foreach ($order->histories as $history) : $author = $history->createdBy; ?>
    <div class="order-chat__item">
        <?php if ($author->role == UserHelper::ROLE_BOT) : ?>
            <img src="/images/bot.png" alt="Bot" class="order-chat__bot">
        <?php else : ?>
            <i class="order-chat__user icon-person"></i>
        <?php endif; ?>
        <div class="order-chat__block">
            <div class="order-chat__author">
                <?= UserHelper::getShortName($author) ?>
                <span class="order-chat__role">(<?= UserHelper::getRoleName($author->role) ?>)</span>
            </div>
            <div class="order-chat__status">
                <?= OrderHelper::getStatusName($history->status_before) ?>
                <i class="order-chat__arrow icon-arrow_forward"></i>
                <?= OrderHelper::getStatusName($history->status_after) ?>
            </div>
        </div>
        <div class="order-chat__date"><?= date('d.m.y H:i', $history->created_at) ?></div>
    </div>
    <?php foreach ($history->orderEvents as $event): ?>
        <div class="order-chat__item order-chat__event" data-id="<?= $event->id ?>">
            <div class="order-chat__block <?= OrderEventHelper::getPriorityClass($event->type) ?>">
                <div class="order-chat__author"><?= UserHelper::getShortName($event->createdBy) ?></div>
                <div class="order-chat__text">
                    <?php if ($image = OrderEventHelper::getImage($event->type)) : ?>
                        <img class="order-chat__image" src="<?= $image ?>" alt="<?= $event->id ?>">
                    <?php endif; ?>
                    <?php if ($icon = OrderEventHelper::getIconClass($event->type)) : ?>
                        <i class="order-chat__icon <?= $icon ?>"></i>
                    <?php endif; ?>
                    <?= TextHelper::getOrderMessage($event) ?>
                </div>
            </div>
            <div class="order-chat__date"><?= date('d.m.y H:i', $event->created_at) ?></div>
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>