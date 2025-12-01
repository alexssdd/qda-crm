<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\widgets\GridView;
use app\entities\Merchant;
use app\search\MerchantSearch;
use yii\data\ActiveDataProvider;
use app\core\helpers\MerchantHelper;

/* @var $this View */
/* @var $searchModel MerchantSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Merchants');
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
                'value' => function (Merchant $model) {
                    return Html::a($model->name, ['update', 'id' => $model->id], ['class' => 'js-view-modal', 'data-pjax' => 0]);
                },
            ],
            [
                'attribute' => 'code',
                'options' => ['width' => 200]
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'options' => ['width' => 200],
                'value' => function (Merchant $model) {
                    return MerchantHelper::getStatusLabel($model->status);
                },
                'filter' => MerchantHelper::getStatusArray()
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
