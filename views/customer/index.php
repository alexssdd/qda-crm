<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\widgets\GridView;
use app\entities\Customer;
use app\search\CustomerSearch;
use app\core\helpers\UserHelper;
use yii\data\ActiveDataProvider;
use app\core\helpers\PhoneHelper;
use app\core\helpers\CustomerHelper;
use app\assets\CustomerAsset;

/* @var $this View */
/* @var $searchModel CustomerSearch */
/* @var $dataProvider ActiveDataProvider */

// View params
$this->title = Yii::t('app', 'Customers');
$this->params['breadcrumbs'][] = $this->title;
CustomerAsset::register($this);
?>
<div class="page">
    <div class="page__header">
        <h1 class="page__title"><?= Html::encode($this->title) ?></h1>
    </div>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'id' => 'customer-grid-view',
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
                'value' => function (Customer $model) {
                    if ($model->source_id !== null) {
                        return Html::encode($model->name ?: '—');
                    }

                    $action = 'update';
                    if (UserHelper::isOperator()){
                        $action = 'detail';
                    }
                    return Html::a(Html::encode($model->name), [$action, 'id' => $model->id], [
                        'class' => 'js-view-modal',
                        'data-pjax' => 0,
                    ]);
                },
            ],
            [
                'attribute' => 'phone',
                'options' => ['width' => 150],
                'value' => function (Customer $model) {
                    return $model->phone
                        ? PhoneHelper::getMaskPhone($model->phone)
                        : '—';
                }
            ],
            [
                'attribute' => 'country_code',
                'label' => Yii::t('app', 'Country'),
                'options' => ['width' => 150],
                'value' => function (Customer $model) {
                    return $model->country?->name ?? $model->country_code ?? '—';
                },
                'filter' => CustomerHelper::getCountries(),
            ],
            [
                'attribute' => 'registered_at',
                'label' => Yii::t('app', 'Registered At'),
                'format' => 'datetime',
                'options' => ['width' => 170],
                'filterInputOptions' => [
                    'class' => 'form-control customer-register-date',
                    'placeholder' => Yii::t('app', 'Period'),
                    'autocomplete' => 'off',
                ],
            ],
            [
                'attribute' => 'orders_created',
                'label' => Yii::t('app', 'Orders Created'),
                'options' => ['width' => 100],
                'filter' => false,
            ],
            [
                'attribute' => 'orders_completed',
                'label' => Yii::t('app', 'Orders Completed'),
                'options' => ['width' => 100],
                'filter' => false,
            ],
            [
                'attribute' => 'orders_canceled',
                'label' => Yii::t('app', 'Orders Canceled'),
                'options' => ['width' => 100],
                'filter' => false,
            ],
            [
                'attribute' => 'last_order_at',
                'label' => Yii::t('app', 'Last Order At'),
                'format' => 'datetime',
                'options' => ['width' => 160],
                'filter' => false,
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'options' => ['width' => 150],
                'value' => function (Customer $model) {
                    return CustomerHelper::getStatusLabel($model->status);
                },
                'filter' => CustomerHelper::getStatusArray()
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
