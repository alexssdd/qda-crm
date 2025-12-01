<?php

/** @var $data [] */

?>
<table class="chart-table animated fadeIn">
    <colgroup>
        <col width="35">
        <col>
        <col width="110">
        <col width="110">
        <col width="110">
        <col width="110">
        <col width="110">
    </colgroup>
    <thead>
        <tr>
            <th>№</th>
            <th>ФИО</th>
            <th>Кол-во продаж</th>
            <th>Сумма продаж</th>
            <th>Кол-во заказов</th>
            <th>Кол-во лидов</th>
            <th>Кол-во запросов</th>
        </tr>
    </thead>
</table>
<div class="chart-table__container">
    <table class="chart-table animated fadeIn">
        <colgroup>
            <col width="35">
            <col>
            <col width="110">
            <col width="110">
            <col width="110">
            <col width="110">
            <col width="110">
        </colgroup>
        <tbody>
        <?php foreach ($data as $i => $item) : ?>
            <tr>
                <td><strong><?= $i + 1 ?></strong></td>
                <td>
                    <div class="chart-table__name"><?= $item['name'] ?></div>
                </td>
                <td><?= $item['count_crm'] ?></td>
                <td><?= Yii::$app->formatter->asDecimal($item['sum_crm']) ?></td>
                <td><?= $item['count'] ?></td>
                <td><?= $item['count_lead'] ?></td>
                <td><?= $item['count_care'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>