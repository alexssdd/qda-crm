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

/* @var $this View */
/* @var $searchModel CustomerSearch */
/* @var $dataProvider ActiveDataProvider */

// View params
$this->title = Yii::t('app', 'Customers');
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
                'value' => function (Customer $model) {
                    $action = 'update';
                    if (UserHelper::isOperator()){
                        $action = 'detail';
                    }
                    return Html::a($model->name, [$action, 'id' => $model->id], ['class' => 'js-view-modal', 'data-pjax' => 0]);
                },
            ],
            [
                'attribute' => 'phone',
                'options' => ['width' => 150],
                'value' => function (Customer $model) {
                    return PhoneHelper::getMaskPhone($model->phone);
                }
            ],
            [
                'attribute' => 'email',
                'options' => ['width' => 200]
            ],
            [
                'attribute' => 'iin',
                'options' => ['width' => 150]
            ],
            [
                'attribute' => 'type',
                'options' => ['width' => 150],
                'filter' => CustomerHelper::getTypeArray(),
                'value' => function (Customer $model) {
                    return CustomerHelper::getTypeName($model->type);
                }
            ],
            [
                'attribute' => 'ref',
                'options' => ['width' => 150]
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
            [
                'format' => 'raw',
                'options' => ['width' => 150],
                'value' => function (Customer $model) {
                    $label = Yii::t('app', 'Addresses') . ' (' . count($model->addresses) . ')';
                    return Html::a($label, ['/address/index', 'customer_id' => $model->id], [
                        'data-pjax' => 0,
                        'target' => '_blank'
                    ]);
                }
            ]
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
