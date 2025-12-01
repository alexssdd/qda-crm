<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\entities\Country;
use app\widgets\GridView;
use app\search\CountrySearch;
use yii\data\ActiveDataProvider;
use app\core\helpers\CountryHelper;

/* @var $this View */
/* @var $searchModel CountrySearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Countries');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page">
    <div class="page__header">
        <h1 class="page__title"><?= $this->title; ?></h1>
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
                'value' => function (Country $model) {
                    return Html::a($model->name, ['update', 'id' => $model->id], ['class' => 'js-view-modal', 'data-pjax' => 0]);
                },
            ],
            [
                'attribute' => 'iso',
                'options' => ['width' => 200]
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'options' => ['width' => 200],
                'value' => function (Country $model) {
                    return CountryHelper::getStatusLabel($model->status);
                },
                'filter' => CountryHelper::getStatusArray()
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
