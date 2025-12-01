<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\entities\Product;
use app\widgets\GridView;
use app\search\ProductSearch;
use yii\data\ActiveDataProvider;
use app\core\helpers\UserHelper;
use app\core\helpers\ProductHelper;
use app\core\helpers\MerchantHelper;

/* @var $this View */
/* @var $searchModel ProductSearch */
/* @var $dataProvider ActiveDataProvider */

// View params
$this->title = Yii::t('app', 'Products');
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
                'value' => function (Product $model) {
                    $action = 'update';
                    if (UserHelper::isOperator()){
                        $action = 'view';
                    }
                    return Html::a($model->name, [$action, 'id' => $model->id], ['class' => 'js-view-modal', 'data-pjax' => 0]);
                },
            ],
            [
                'attribute' => 'sku',
                'options' => ['width' => 200]
            ],
            [
                'attribute' => 'merchant_id',
                'options' => ['width' => 150],
                'value' => function (Product $model) {
                    return $model->merchant ? $model->merchant->name : '';
                },
                'filter' => MerchantHelper::getSelectArray()
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'options' => ['width' => 200],
                'value' => function (Product $model) {
                    return ProductHelper::getStatusLabel($model->status);
                },
                'filter' => ProductHelper::getStatusArray()
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
