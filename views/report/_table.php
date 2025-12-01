<?php

use yii\web\View;
use yii\helpers\Html;
use app\entities\Report;
use app\widgets\GridView;
use app\search\ReportSearch;
use yii\data\ActiveDataProvider;
use app\core\helpers\ReportHelper;

/** @var $this View */
/** @var $searchModel ReportSearch */
/** @var $dataProvider ActiveDataProvider */

?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'options' => ['width' => 45]
        ],
        [
            'attribute' => 'date_from',
            'options' => ['width' => 150],
        ],
        [
            'attribute' => 'date_to',
            'options' => ['width' => 150],
        ],
        [
            'attribute' => 'comment'
        ],
        [
            'attribute' => 'created_at',
            'options' => ['width' => 150],
            'format' => 'dateTime'
        ],
        [
            'attribute' => 'status',
            'format' => 'raw',
            'options' => ['width' => 115],
            'filter' => ReportHelper::getStatusArray(),
            'value' => function (Report $model){
                return ReportHelper::getStatusLabel($model->status);
            }
        ],
        [
            'format' => 'raw',
            'options' => ['width' => 110],
            'value' => function (Report $model){
                if ($model->status !== ReportHelper::STATUS_DONE){
                    return '';
                }

                return Html::a('Скачать', ['/report/download', 'id' => $model->id], [
                    'data-pjax' => 0,
                    'download' => true
                ]);
            }
        ]
    ],
]); ?>