<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\widgets\GridView;
use app\search\CountrySearch;
use yii\data\ActiveDataProvider;
use app\core\helpers\CountryHelper;
use app\modules\location\models\Country;

/* @var $this View */
/* @var $searchModel CountrySearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Countries');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page">
    <div class="page__header">
        <h1 class="page__title"><?= $this->title; ?></h1>
        <div class="page__actions">
            <a class="btn btn__primary js-view-modal" href="/pharmacy/create">Добавить</a>
        </div>
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
                'options' => ['width' => 200],
                'value' => function (Country $model) {
                    return Html::a($model->name, ['update', 'id' => $model->id], ['class' => 'js-view-modal', 'data-pjax' => 0]);
                },
            ],
            [
                'attribute' => 'code',
                'options' => ['width' => 200]
            ],
            'phone_code',
            'client_api_url',
            'pro_api_url',
            'created_at',
            'updated_at',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'options' => ['width' => 100],
                'value' => function (Country $model) {
                    return CountryHelper::getStatusLabel($model->status);
                },
                'filter' => CountryHelper::getStatusArray()
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
