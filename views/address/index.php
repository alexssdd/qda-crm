<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\entities\Address;
use app\widgets\GridView;
use app\entities\Customer;
use app\search\AddressSearch;
use app\core\helpers\CityHelper;
use yii\data\ActiveDataProvider;
use app\core\helpers\AddressHelper;

/* @var $this View */
/* @var $customer Customer */
/* @var $searchModel AddressSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = $customer->name . ' - ' . Yii::t('app', 'Addresses');
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
                'attribute' => 'address',
                'format' => 'raw',
                'value' => function (Address $model) {
                    return Html::a($model->address, ['update', 'id' => $model->id], ['class' => 'js-view-modal', 'data-pjax' => 0]);
                },
            ],
            [
                'attribute' => 'city_id',
                'options' => ['width' => 200],
                'value' => function (Address $model) {
                    return $model->city ? $model->city->name : '';
                },
                'filter' => CityHelper::getSelectArray()
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'options' => ['width' => 200],
                'value' => function (Address $model) {
                    return AddressHelper::getStatusLabel($model->status);
                },
                'filter' => AddressHelper::getStatusArray()
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
