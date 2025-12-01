<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\widgets\GridView;
use app\entities\PriceType;
use app\search\PriceTypeSearch;
use app\core\helpers\DataHelper;
use yii\data\ActiveDataProvider;
use app\core\helpers\PriceTypeHelper;

/* @var $this View */
/* @var $searchModel PriceTypeSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Price types');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page">
    <div class="page__header">
        <h1 class="page__title"><?= $this->title; ?></h1>
        <a class="btn btn--primary js-view-modal" href="<?= Url::to(['create']) ?>"><?= Yii::t('app', 'Create')?></a>
    </div>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'options' => ['width' => 45]
            ],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function (PriceType $model) {
                    return Html::a($model->name, ['update', 'id' => $model->id], ['class' => 'js-view-modal', 'data-pjax' => 0]);
                },
            ],
            [
                'attribute' => 'code',
                'options' => ['width' => 200]
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'options' => ['width' => 200],
                'value' => function (PriceType $model) {
                    return PriceTypeHelper::getStatusLabel($model->status);
                },
                'filter' => PriceTypeHelper::getStatusArray()
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
