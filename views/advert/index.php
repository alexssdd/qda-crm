<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\entities\Advert;
use app\widgets\GridView;
use app\search\AdvertSearch;
use yii\data\ActiveDataProvider;
use app\core\helpers\AdvertHelper;

/* @var $this View */
/* @var $searchModel AdvertSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Adverts');
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
                'value' => function (Advert $model) {
                    return Html::a($model->name, ['update', 'id' => $model->id], ['class' => 'js-view-modal', 'data-pjax' => 0]);
                },
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'options' => ['width' => 200],
                'value' => function (Advert $model) {
                    return AdvertHelper::getStatusLabel($model->status);
                },
                'filter' => AdvertHelper::getStatusArray()
            ],
            [
                'attribute' => 'begin_at',
                'format' => 'raw',
                'options' => ['width' => 200],
                'value' => function (Advert $model) {
                    return Yii::$app->formatter->asDatetime($model->begin_at, 'php:d.m.Y H:i');
                }
            ],
            [
                'attribute' => 'end_at',
                'format' => 'raw',
                'options' => ['width' => 200],
                'value' => function (Advert $model) {
                    return Yii::$app->formatter->asDatetime($model->end_at, 'php:d.m.Y H:i');
                }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
