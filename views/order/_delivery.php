<?php

use yii\web\View;
use app\entities\Order;
use app\core\helpers\OrderHelper;

/* @var $this View */
/* @var $order Order */

?>
<div class="modal__container">
    <div class="modal__title">Детали доставки</div>
    <div class="modal__body">
        <table class="table table-striped table-bordered detail-view">
            <colgroup>
                <col width="175">
                <col>
            </colgroup>
            <tbody>
                <tr>
                    <th>Дата отгрузки</th>
                </tr>
                <tr>
                    <th>Стоимость доставки</th>
                    <td></td>
                </tr>
                <tr>
                    <th>Код доставки</th>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="modal__footer">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
    </div>
    <i class="modal__close icon-close"></i>
</div>