<?php

use yii\web\View;
use app\entities\Order;
use yii\helpers\ArrayHelper;
use app\entities\OrderReceipt;
use app\entities\OrderProduct;
use app\core\helpers\OrderReceiptHelper;

/* @var $this View */
/* @var $order Order */
/* @var $receipt OrderReceipt */
/* @var $orderProducts OrderProduct[] */

$title = 'Чек продажи #' . $receipt->id;
if ($receipt->type == OrderReceiptHelper::TYPE_RETURN){
    $title = 'Чек возврата #' . $receipt->id;
}

?>
<div class="modal__container modal__container--800">
    <div class="modal__title"><?= $title ?> для заказа #<?= $order->number ?></div>
    <div class="modal__body">
        <table class="modal-table">
            <thead>
                <tr>
                    <th class="modal-table__td--85">SKU</th>
                    <th class="modal-table__td--512">Город</th>
                    <th class="modal-table__td--85">Кол-во</th>
                    <th class="modal-table__td--85">Цена</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($receipt->products as $product) :
                /** @var OrderProduct $orderProduct */
                $orderProduct = ArrayHelper::getValue($orderProducts, $product['sku']);
                ?>
                <tr>
                    <td class="modal-table__td--85"><?= abs($product['sku']) ?></td>
                    <td class="modal-table__td--512 modal-table__break"><?= $orderProduct ? $orderProduct->name : '' ?></td>
                    <td class="modal-table__td--85"><?= Yii::$app->formatter->asDecimal(abs($product['quantity'])) ?></td>
                    <td class="modal-table__td--85"><?= $orderProduct ? Yii::$app->formatter->asDecimal($orderProduct->price) : '' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="modal__footer modal__footer--bordered">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
    </div>
    <i class="modal__close icon-close"></i>
</div>