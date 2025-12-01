<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\widgets\GridView;
use app\entities\Customer;
use app\entities\Contract;
use app\search\ContractSearch;
use yii\data\ActiveDataProvider;
use app\core\helpers\MerchantHelper;
use app\core\helpers\ContractHelper;

/* @var $this View */
/* @var $customer Customer */
/* @var $searchModel ContractSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = $customer->name . ' - ' . Yii::t('app', 'Contracts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page">
    <div class="page__header">
        <h1 class="page__title"><?= $this->title; ?></h1>
        <a class="btn btn--primary js-view-modal" href="<?= Url::to(['create', 'customer_id' => $customer->id]) ?>"><?= Yii::t('app', 'Create')?></a>
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
                'attribute' => 'number',
                'format' => 'raw',
                'value' => function (Contract $model) {
                    return Html::a($model->number, ['update', 'id' => $model->id], ['class' => 'js-view-modal', 'data-pjax' => 0]);
                },
            ],
            [
                'attribute' => 'merchant_id',
                'options' => ['width' => 200],
                'value' => function (Contract $model) {
                    return $model->merchant ? $model->merchant->name : '';
                },
                'filter' => MerchantHelper::getSelectArray()
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'options' => ['width' => 200],
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'options' => ['width' => 200],
                'value' => function (Contract $model) {
                    return ContractHelper::getStatusLabel($model->status);
                },
                'filter' => ContractHelper::getStatusArray()
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
