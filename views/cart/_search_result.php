<?php

use yii\helpers\StringHelper;

/** @var $result [] */

?>
<?php foreach ($result as $i => $item) : ?>
    <tr class="modal-table__selector" data-id="<?= $item['id'] ?>" data-sku="<?= $item['sku']?>" data-price="<?= Yii::$app->formatter->asDecimal($item['price']) ?>">
        <td class="modal-table__td--35 text-center"><?= $i + 1 ?></td>
        <td class="modal-table__td--85 cart-products__sku text-center"><?= $item['sku'] ?></td>
        <td class="modal-table__td--527 cart-products__name"><?= StringHelper::truncate($item['name'], 75) ?></td>
        <td class="modal-table__td--150"><?= $item['brand'] ?></td>
        <td class="modal-table__td--85 text-center"><?= $item['stock'] ?></td>
        <td class="modal-table__td--85 text-center"><?= Yii::$app->formatter->asDecimal($item['price']) ?></td>
    </tr>
<?php endforeach; ?>
