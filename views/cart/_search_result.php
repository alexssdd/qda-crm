<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;

/** @var $result [] */

?>
<?php foreach ($result as $i => $item) : ?>
    <tr class="modal-table__selector" data-id="<?= (int) $item['id'] ?>" data-sku="<?= Html::encode((string) $item['sku']) ?>" data-price="<?= Html::encode(Yii::$app->formatter->asDecimal($item['price'])) ?>">
        <td class="modal-table__td--35 text-center"><?= $i + 1 ?></td>
        <td class="modal-table__td--85 cart-products__sku text-center"><?= Html::encode((string) $item['sku']) ?></td>
        <td class="modal-table__td--527 cart-products__name"><?= Html::encode(StringHelper::truncate((string) $item['name'], 75)) ?></td>
        <td class="modal-table__td--150"><?= Html::encode((string) $item['brand']) ?></td>
        <td class="modal-table__td--85 text-center"><?= Html::encode((string) $item['stock']) ?></td>
        <td class="modal-table__td--85 text-center"><?= Yii::$app->formatter->asDecimal($item['price']) ?></td>
    </tr>
<?php endforeach; ?>
