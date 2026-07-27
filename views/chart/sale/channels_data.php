<?php

use yii\helpers\Html;
use app\core\helpers\TextHelper;

/** @var $data [] */

?>
<table class="chart-table animated fadeIn">
    <colgroup>
        <col width="35">
        <col>
        <col width="15%">
        <col width="15%">
        <col width="15%">
    </colgroup>
    <thead>
        <tr>
            <th>№</th>
            <th>Название</th>
            <th>%</th>
            <th>Кол-во</th>
            <th>Сумма</th>
        </tr>
    </thead>
</table>
<div class="chart-table__container">
    <table class="chart-table animated fadeIn">
        <colgroup>
            <col width="35">
            <col>
            <col width="15%">
            <col width="15%">
            <col width="15%">
        </colgroup>
        <tbody>
        <?php foreach ($data as $i => $item) : ?>
            <tr>
                <td><strong><?= $i + 1 ?></strong></td>
                <td>
                    <div class="chart-table__name"><?= Html::encode((string) $item['name']) ?></div>
                </td>
                <td><?= Html::encode((string) $item['percent']) ?>%</td>
                <td><?= Html::encode((string) $item['count']) ?></td>
                <td><?= Html::encode((string) TextHelper::getShortNumber($item['sum'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
