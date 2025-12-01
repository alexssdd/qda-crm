<?php

use app\core\helpers\DateHelper;

/** @var $data [] */

?>
<div class="chart-table__container animated fadeIn">
    <table class="chart-table chart-table--only-body">
        <tbody>
        <?php foreach ($data as $item) : if (!$item['count']) continue; ?>
            <tr>
                <td>
                    <div class="chart-table__name"><?= $item['name'] ?></div>
                </td>
                <td class="chart-table__right"><?= DateHelper::getGmDate($item['time_average']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>