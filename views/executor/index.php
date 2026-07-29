<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\widgets\GridView;
use app\assets\ExecutorAsset;
use yii\data\ActiveDataProvider;
use app\search\ExecutorSearch;
use app\modules\order\models\Executor;
use app\modules\order\helpers\ExecutorHelper;

/* @var $this View */
/* @var $searchModel ExecutorSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Executors');
$this->params['breadcrumbs'][] = $this->title;

ExecutorAsset::register($this);
?>
<div class="page">
    <div class="page__header">
        <h1 class="page__title"><?= Html::encode($this->title) ?></h1>
    </div>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'id' => 'executor-grid-view',
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
                'format' => 'raw',
                'options' => ['width' => 220],
                'value' => static function (Executor $model): string {
                    return Html::a(
                        Html::encode($model->name),
                        ['update', 'id' => $model->id],
                        ['class' => 'js-view-modal', 'data-pjax' => 0]
                    );
                },
            ],
            [
                'attribute' => 'phone',
                'label' => Yii::t('app', 'Phone'),
                'format' => 'raw',
                'options' => ['width' => 170],
                'value' => static function (Executor $model): string {
                    $masked = '***';
                    $value = Html::tag('span', $masked, [
                        'class' => 'executor-phone__value',
                        'aria-live' => 'polite',
                    ]);
                    $button = Html::button('Показать', [
                        'type' => 'button',
                        'class' => 'executor-phone__toggle js-executor-phone-toggle',
                        'data-url' => Url::to(['/executor/phone', 'id' => $model->id]),
                        'data-masked' => $masked,
                        'aria-label' => 'Показать телефон исполнителя',
                    ]);

                    return Html::tag('span', $value . $button, [
                        'class' => 'executor-phone',
                    ]);
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
                'attribute' => 'location_name',
                'label' => Yii::t('app', 'Location'),
                'options' => ['width' => 180],
                'value' => static function (Executor $model): string {
                    return $model->location?->name ?? '—';
                },
            ],
            [
                'attribute' => 'service_type',
                'label' => Yii::t('app', 'Services'),
                'options' => ['width' => 210],
                'value' => static function (Executor $model): string {
                    return ExecutorHelper::getServiceNames($model) ?: '—';
                },
                'filter' => ExecutorHelper::getServiceTypes(),
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
                'attribute' => 'registered_at',
                'label' => Yii::t('app', 'Registered At'),
                'format' => 'datetime',
                'options' => ['width' => 170],
                'filterInputOptions' => [
                    'class' => 'form-control executor-register-date',
                    'placeholder' => Yii::t('app', 'Period'),
                    'autocomplete' => 'off',
                ],
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
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
