<?php

/** @var $result [] */

?>
<?php foreach ($result as $i => $item) : ?>
    <tr class="modal-table__selector" data-id="<?= $item['id'] ?>">
        <td class="modal-table__td--85 text-center"><?= $item['sku'] ?></td>
        <td class="modal-table__td--100 text-center"><?= $item['barcode'] ?></td>
        <td class="modal-table__td--377 modal-table__break"><?= $item['name'] ?></td>
        <td class="modal-table__td--150 text-center"><?= $item['brand'] ?></td>
        <td class="modal-table__td--85 text-center"><?= Yii::$app->formatter->asDecimal($item['stock']) ?></td>
        <td class="modal-table__td--85">
            <input type="number" class="modal-table__input text-center" min="1" max="<?= $item['stock'] ?: 1 ?>" value="1">
        </td>
        <td class="modal-table__td--85 text-center"><?= Yii::$app->formatter->asDecimal($item['price']) ?></td>
    </tr>
<?php endforeach; ?>
