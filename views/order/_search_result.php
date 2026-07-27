<?php

use yii\helpers\Html;

/** @var $result [] */

?>
<?php foreach ($result as $i => $item) : ?>
    <tr class="modal-table__selector" data-id="<?= (int) $item['id'] ?>">
        <td class="modal-table__td--85 text-center"><?= Html::encode((string) $item['sku']) ?></td>
        <td class="modal-table__td--100 text-center"><?= Html::encode((string) $item['barcode']) ?></td>
        <td class="modal-table__td--377 modal-table__break"><?= Html::encode((string) $item['name']) ?></td>
        <td class="modal-table__td--150 text-center"><?= Html::encode((string) $item['brand']) ?></td>
        <td class="modal-table__td--85 text-center"><?= Yii::$app->formatter->asDecimal($item['stock']) ?></td>
        <td class="modal-table__td--85">
            <input type="number" class="modal-table__input text-center" min="1" max="<?= max(1, (int) $item['stock']) ?>" value="1">
        </td>
        <td class="modal-table__td--85 text-center"><?= Yii::$app->formatter->asDecimal($item['price']) ?></td>
    </tr>
<?php endforeach; ?>
