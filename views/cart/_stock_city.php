<?php

use yii\web\View;
use app\entities\Product;

/* @var $this View */
/* @var $cities [] */
/* @var $product Product */

?>
<div class="modal__container modal__container--500">
    <div class="modal__title"><?= $product->name ?></div>
    <div class="modal__body">
        <table class="modal-table">
            <thead>
                <tr>
                    <th class="modal-table__td--382">Город</th>
                    <th class="modal-table__td--85">Остаток</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($cities as $city) : ?>
                <tr>
                    <td class="modal-table__td--382"><?= $city['name'] ?></td>
                    <td class="modal-table__td--85"><?= $city['stock'] ?></td>
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