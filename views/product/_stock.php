<?php

use yii\web\View;
use app\entities\Product;

/* @var $this View */
/* @var $stocks [] */
/* @var $model Product */

?>
<div class="modal__container modal__container--500">
    <div class="modal__title"><?= $model->name ?></div>
    <div class="modal__body">
        <table class="modal-table">
            <thead>
            <tr>
                <th class="modal-table__td--382">Точка продажи</th>
                <th class="modal-table__td--85">Остаток</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($stocks as $city) : ?>
                <tr class="modal-table__tr--group">
                    <td class="modal-table__td--382"><?= $city['name'] ?></td>
                    <td class="modal-table__td--85"><?= Yii::$app->formatter->asDecimal($city['quantity']) ?></td>
                </tr>
                <?php foreach ($city['stores'] as $store) : ?>
                <tr>
                    <td class="modal-table__td--382"><?= $store['name'] ?></td>
                    <td class="modal-table__td--85"><?= Yii::$app->formatter->asDecimal($store['quantity']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="modal__footer modal__footer--bordered">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
    </div>
    <i class="modal__close icon-close"></i>
</div>