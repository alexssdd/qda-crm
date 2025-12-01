<?php

use yii\helpers\Url;
use app\entities\Lead;
use app\core\helpers\LeadHelper;

/** @var $lead Lead */

// Variables
$customer = $lead->customer;

?>
<div class="lead-top">
    <div class="lead-top__title"><?= LeadHelper::getTitle($lead) ?></div>
    <div class="lead-top__actions">
        <?php if ($lead->phone) : ?>
        <a href="tel:<?= $lead->phone ?>" class="lead-top__action lead-top__action--icon transition icon-call" target="_blank"></a>
        <a href="https://wa.me/<?= $lead->phone ?>" class="lead-top__action lead-top__action--icon transition icon-whatsapp" target="_blank"></a>
        <?php endif; ?>
        <?php if ($customer && $customer->email) : ?>
            <a href="mailto:<?= $customer->email ?>" class="lead-top__action lead-top__action--icon transition icon-email" target="_blank"></a>
        <?php endif; ?>
        <a href="<?= Url::to(['/order/index', 'lead_id' => $lead->id]) ?>" class="lead-top__action" target="_blank">Заказ</a>
        <a href="<?= Url::to(['/care/index', 'lead_id' => $lead->id]) ?>" class="lead-top__action" target="_blank">Обращение</a>
    </div>
</div>