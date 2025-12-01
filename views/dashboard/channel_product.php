<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\assets\DashboardAsset;
use app\core\helpers\CityHelper;
use app\core\helpers\OrderHelper;
use app\search\dashboard\ChannelSearch;

/** @var $data [] */
/** @var $this View */
/** @var $searchModel ChannelSearch */

// Assets
DashboardAsset::register($this);

// View params
$this->title = 'Топы продаж и их остатки';

?>
<div class="dashboard page">
    <div class="page__header">
        <h1 class="page__title"><?= $this->title; ?></h1>
        <form class="page-filter" action="<?= Url::canonical() ?>" method="GET">
            <div class="page-filter__item">
                <?= Html::activeDropDownList($searchModel, 'channel', OrderHelper::getChannels(), [
                    'class' => 'page-filter__input',
                    'prompt' => 'Выберите канал'
                ]) ?>
            </div>
            <div class="page-filter__item">
                <?= Html::activeTextInput($searchModel, 'date_range', [
                    'class' => 'page-filter__input page-filter__input--date',
                    'prompt' => 'Выберите период'
                ]) ?>
            </div>
        </form>
    </div>
    <div class="dashboard">
        <div class="dashboard__body">
            <div class="dashboard__box">
                <table class="dashboard-table">
                    <colgroup>
                        <col width="45">
                        <col width="125">
                        <col>
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
                            <th class="dashboard-table__th dashboard-table__th--yellow" rowspan="2">#</th>
                            <th class="dashboard-table__th dashboard-table__th--yellow" rowspan="2">Код товара</th>
                            <th class="dashboard-table__th dashboard-table__th--yellow" rowspan="2">Товар</th>
                            <th class="dashboard-table__th dashboard-table__th--yellow" rowspan="2">Кол-во продаж</th>
                            <th class="dashboard-table__th dashboard-table__th--yellow" rowspan="2">Сумма продаж</th>
                            <th class="dashboard-table__th dashboard-table__th--yellow" rowspan="2">%</th>
                            <th class="dashboard-table__th dashboard-table__th--orange" colspan="4">Остатки</th>
                        </tr>
                        <tr>
                            <th class="dashboard-table__th dashboard-table__th--orange">Алматы</th>
                            <th class="dashboard-table__th dashboard-table__th--orange">Астана</th>
                            <th class="dashboard-table__th dashboard-table__th--orange">Шымкент</th>
                            <th class="dashboard-table__th dashboard-table__th--orange">Другие</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $i => $product) : ?>
                        <tr>
                            <td class="dashboard-table__td text-center"><?= $i + 1 ?></td>
                            <td class="dashboard-table__td text-center"><?= $product['sku'] ?></td>
                            <td class="dashboard-table__td"><?= $product['name'] ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($product['quantity']) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($product['sum']) ?></td>
                            <td class="dashboard-table__td text-center"><?= $product['percent'] ?>%</td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($product['cityStocks'][CityHelper::ID_ALMATY]) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($product['cityStocks'][CityHelper::ID_ASTANA]) ?></td>
                            <td class="dashboard-table__td text-center"><?= Yii::$app->formatter->asDecimal($product['cityStocks'][CityHelper::ID_SHYMKENT]) ?></td>
                            <td class="dashboard-table__td text-center">
                                <a href="<?= Url::to(['/product/stock', 'sku' => $product['sku']]) ?>" class="js-view-modal dashboard-table__link">
                                    <?= Yii::$app->formatter->asDecimal($product['cityStocks']['others']) ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>