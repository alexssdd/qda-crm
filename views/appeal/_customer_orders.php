<?php

use yii\web\View;
use yii\helpers\Url;
use app\entities\Order;
use app\entities\Customer;
use app\core\helpers\OrderHelper;

/* @var $this View */
/* @var $data Order[] */
/* @var $customer Customer */

?>
<div class="modal__container modal__container--500">
    <div class="modal__title">Заказы клиента: <?= $customer->name ?></div>
    <div class="modal__body">
        <table class="modal-table">
            <thead>
            <tr>
                <th class="modal-table__td--150">Номер</th>
                <th class="modal-table__td--150">Дата</th>
                <th class="modal-table__td--167">Канал</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($data as $order) : ?>
                <tr>
                    <td class="modal-table__td--150">
                        <a href="<?= Url::to(['/order/index', 'id' => $order->id]) ?>" target="_blank"><?= $order->number ?></a>
                    </td>
                    <td class="modal-table__td--150"><?= Yii::$app->formatter->asDatetime($order->created_at) ?></td>
                    <td class="modal-table__td--167"><?= OrderHelper::getChannel($order->channel) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="modal__footer modal__footer--bordered">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.closeAdditional()"><?= Yii::t('app', 'Close'); ?></a>
    </div>
    <i class="modal__close icon-close"></i>
</div>