<?php

use app\entities\Log;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\Pjax;
use yii\grid\GridView;
use app\search\LogSearch;
use yii\widgets\LinkPager;
use yii\helpers\StringHelper;
use app\core\helpers\LogHelper;

/* @var $this yii\web\View */
/* @var $searchModel LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Logs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page log-index">
    <div class="page__header">
        <h1 class="page__title"><?= $this->title; ?></h1>
    </div>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{summary}\n{items}",
        'columns' => [
            ['class' => 'app\core\SerialColumn'],

            [
                'attribute' => 'target',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width: 150px;'],
                'value' => function (Log $model) {
                    return Html::a(
                        LogHelper::targetName($model->target),
                        ['view', 'id' => $model->id],
                        ['data-pjax' => 0, 'class' => 'js-view-modal']
                    );
                },
                'filter' => LogHelper::targetList()
            ],
            [
                'attribute' => 'data',
                'headerOptions' => ['style' => 'width: 450px;'],
                'value' => function (Log $model) {
                    return StringHelper::truncate(Json::encode($model->data), 130);
                },
            ],
            [
                'attribute' => 'runtime',
                'headerOptions' => ['style' => 'width: 50px;'],
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'headerOptions' => ['style' => 'width: 100px;'],
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width: 80px;'],
                'value' => function (Log $model) {
                    return LogHelper::statusLabel($model->status);
                },
                'filter' => LogHelper::statusList()
            ],
        ],
    ]); ?>

    <?= LinkPager::widget([
        'pagination' => $dataProvider->getPagination(),
        'options' => [
            'class' => 'pagination',
        ],
        'linkContainerOptions'=>['class' => 'pagination__item'],
        'linkOptions'=>['class' => 'pagination__link'],
        'disabledListItemSubTagOptions'=>[
            'tag' => 'a',
            'class' => 'pagination__link pagination__link--disabled'
        ],
        'prevPageLabel' => Yii::t('app', 'Keep off'),
        'nextPageLabel' => Yii::t('app', 'Forward'),
    ]) ?>
    <?php Pjax::end(); ?>
</div>
