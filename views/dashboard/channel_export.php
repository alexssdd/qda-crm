<?php

use yii\web\View;
use yii\helpers\Json;
use app\assets\DashboardAsset;
use app\search\dashboard\ChannelSearch;

/** @var $data [] */
/** @var $this View */
/** @var $searchModel ChannelSearch */

// Assets
DashboardAsset::register($this);

// View params
$this->title = 'Остатки по каналам';

?>
<div class="dashboard page">
    <div class="page__header">
        <h1 class="page__title"><?= $this->title; ?></h1>
    </div>
    <div class="dashboard">
        <div class="dashboard__body">
            <div class="dashboard__box">
                <table class="dashboard-table">
                    <colgroup>
                        <col>
                        <col width="125">
                        <col width="125">
                        <col width="125">
                        <col width="125">
                        <col width="125">
                        <col width="125">
                        <col width="125">
                        <col width="125">
                        <col width="125">
                        <col width="125">
                        <col width="125">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="dashboard-table__th dashboard-table__th--yellow" rowspan="2">Точки продаж</th>
                            <th class="dashboard-table__th dashboard-table__th--yellow" colspan="2">Всего</th>
                            <th class="dashboard-table__th dashboard-table__th--orange" colspan="2">Kaspi</th>
                            <th class="dashboard-table__th dashboard-table__th--orange" colspan="2">Wildberries</th>
                            <th class="dashboard-table__th dashboard-table__th--orange" colspan="2">Ozon</th>
                            <th class="dashboard-table__th dashboard-table__th--orange" colspan="2">Wolt</th>
                            <th class="dashboard-table__th dashboard-table__th--yellow" rowspan="2">Все каналы</th>
                        </tr>
                        <tr>
                            <th class="dashboard-table__th dashboard-table__th--yellow">Каналы</th>
                            <th class="dashboard-table__th dashboard-table__th--yellow">Остатки</th>
                            <th class="dashboard-table__th dashboard-table__th--orange">Выгрузка</th>
                            <th class="dashboard-table__th dashboard-table__th--orange">Остатки</th>
                            <th class="dashboard-table__th dashboard-table__th--orange">Выгрузка</th>
                            <th class="dashboard-table__th dashboard-table__th--orange">Остатки</th>
                            <th class="dashboard-table__th dashboard-table__th--orange">Выгрузка</th>
                            <th class="dashboard-table__th dashboard-table__th--orange">Остатки</th>
                            <th class="dashboard-table__th dashboard-table__th--orange">Выгрузка</th>
                            <th class="dashboard-table__th dashboard-table__th--orange">Остатки</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $id => $city) : ?>
                        <tr class="dashboard-table__tr--group">
                            <?php if ($id == 'others') : ?>
                            <td class="dashboard-table__td" colspan="12"><?= $city['name'] ?></td>
                            <?php else : ?>
                            <td class="dashboard-table__td"><?= $city['name'] ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($city['total_channels']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($city['total_stock']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($city['kaspi_export']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($city['kaspi_stock']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($city['wb_export']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($city['wb_stock']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($city['ozon_export']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($city['ozon_stock']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($city['wolt_export']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($city['wolt_stock']) ?></td>
                            <td class="dashboard-table__td text-center"><a class="dashboard-table__link" href="#" onclick="ChannelExport.detailCity('<?= $id ?>')">Подробнее</a></td>
                            <?php endif; ?>
                        </tr>
                        <?php foreach ($city['stores'] as $storeId => $store) : ?>
                        <tr>
                            <td class="dashboard-table__td"><?= $store['name'] ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($store['total_channels']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($store['total_stock']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($store['kaspi_export']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($store['kaspi_stock']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($store['wb_export']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($store['wb_stock']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($store['ozon_export']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($store['ozon_stock']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($store['wolt_export']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($store['wolt_stock']) ?></td>
                            <td class="dashboard-table__td text-center"><a class="dashboard-table__link" href="#" onclick="ChannelExport.detailStore('<?= $id ?>', <?= $storeId ?>)">Подробнее</a></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<template id="templateDetail">
    <div class="modal__container modal__container--500">
        <div class="modal__title">{name}</div>
        <div class="modal__body">
            <table class="modal-table">
                <thead>
                    <tr>
                        <th class="modal-table__td--297">Город</th>
                        <th class="modal-table__td--85">Выгрузка</th>
                        <th class="modal-table__td--85">Остатки</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="modal__footer modal__footer--bordered">
            <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()">Закрыть</a>
        </div>
        <i class="modal__close icon-close"></i>
    </div>
</template>
<template id="templateDetailRow">
    <tr>
        <td class="modal-table__td--297">{name}</td>
        <td class="modal-table__td--85">{export}</td>
        <td class="modal-table__td--85">{stock}</td>
    </tr>
</template>
<?php

$dataJson = Json::encode($data);

$js = <<<JS

ChannelExport.data = $dataJson;

JS;

$this->registerJs($js);

?>