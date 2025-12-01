<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\entities\City;
use app\widgets\GridView;
use app\search\CitySearch;
use yii\data\ActiveDataProvider;
use app\core\helpers\CityHelper;
use app\core\helpers\CountryHelper;

/* @var $this View */
/* @var $searchModel CitySearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Cities');
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
                'value' => function (City $model) {
                    return Html::a($model->name, ['update', 'id' => $model->id], ['class' => 'js-view-modal', 'data-pjax' => 0]);
                },
            ],
            'name_kk',
            [
                'attribute' => 'country_id',
                'options' => ['width' => 200],
                'value' => function (City $model) {
                    return $model->country ? $model->country->name : '';
                },
                'filter' => CountryHelper::getSelectArray()
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'options' => ['width' => 200],
                'value' => function (City $model) {
                    return CityHelper::getStatusLabel($model->status);
                },
                'filter' => CityHelper::getStatusArray()
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>