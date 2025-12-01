<?php

use yii\helpers\Html;

/** @var $data [] */

?>
<?php if ($data) : ?>
    <table class="chart-table animated fadeIn">
        <colgroup>
            <col width="35">
            <col>
            <col width="110">
            <col width="110">
        </colgroup>
        <thead>
            <tr>
                <th>№</th>
                <th>Оператор</th>
                <th class="chart-table__right">Более 20 минут</th>
                <th class="chart-table__right">Отложенные</th>
            </tr>
        </thead>
    </table>
    <div class="chart-table__container chart-table__container--right">
        <table class="chart-table animated fadeIn">
            <colgroup>
                <col width="35">
                <col>
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
                    <td class="chart-table__right">
                        <?= Html::a($item['count_20'], $item['url_20'], ['target' => '_blank']) ?>
                    </td>
                    <td class="chart-table__right">
                        <?= Html::a($item['count_pending'], $item['url_pending'], ['target' => '_blank']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else : ?>
    <div class="chart-success">
        <i class="icon-check chart-success__icon"></i>
    </div>
<?php endif; ?>