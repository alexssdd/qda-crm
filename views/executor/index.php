<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\widgets\GridView;
use yii\data\ActiveDataProvider;
use app\search\ExecutorSearch;
use app\core\helpers\PhoneHelper;
use app\modules\order\models\Executor;
use app\modules\order\helpers\ExecutorHelper;

/* @var $this View */
/* @var $searchModel ExecutorSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Executors');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page">
    <div class="page__header">
        <h1 class="page__title"><?= Html::encode($this->title) ?></h1>
    </div>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'options' => ['width' => 45],
            ],
            [
                'attribute' => 'name',
                'label' => Yii::t('app', 'Executor Name'),
                'options' => ['width' => 220],
            ],
            [
                'attribute' => 'phone',
                'label' => Yii::t('app', 'Phone'),
                'options' => ['width' => 170],
                'value' => static function (Executor $model): string {
                    return PhoneHelper::getMaskPhone($model->phone);
                },
            ],
            [
                'attribute' => 'country_code',
                'label' => Yii::t('app', 'Country'),
                'options' => ['width' => 160],
                'value' => static function (Executor $model): string {
                    return $model->country?->name ?? strtoupper($model->country_code);
                },
                'filter' => ExecutorHelper::getCountries(),
            ],
            [
                'attribute' => 'rating',
                'label' => Yii::t('app', 'Rating'),
                'format' => ['decimal', 2],
                'options' => ['width' => 110],
            ],
            [
                'attribute' => 'is_verified',
                'label' => Yii::t('app', 'Verified'),
                'format' => 'raw',
                'options' => ['width' => 130],
                'value' => static function (Executor $model): string {
                    return ExecutorHelper::getVerificationLabel((bool) $model->is_verified);
                },
                'filter' => ExecutorHelper::getVerificationOptions(),
            ],
            [
                'attribute' => 'orders_completed',
                'label' => Yii::t('app', 'Orders Completed'),
                'format' => 'integer',
                'options' => ['width' => 130],
            ],
            [
                'attribute' => 'orders_canceled',
                'label' => Yii::t('app', 'Orders Canceled'),
                'format' => 'integer',
                'options' => ['width' => 130],
            ],
            [
                'attribute' => 'status',
                'label' => Yii::t('app', 'Status'),
                'format' => 'raw',
                'options' => ['width' => 140],
                'value' => static function (Executor $model): string {
                    return ExecutorHelper::getStatusLabel((int) $model->status);
                },
                'filter' => ExecutorHelper::getStatuses(),
            ],
            [
                'attribute' => 'updated_at',
                'label' => Yii::t('app', 'Updated At'),
                'format' => 'datetime',
                'options' => ['width' => 150],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
