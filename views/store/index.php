<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\entities\Store;
use app\widgets\GridView;
use app\search\StoreSearch;
use yii\data\ActiveDataProvider;
use app\core\helpers\CityHelper;
use app\core\helpers\UserHelper;
use app\core\helpers\StoreHelper;

/* @var $this View */
/* @var $searchModel StoreSearch */
/* @var $dataProvider ActiveDataProvider */

// View params
$this->title = Yii::t('app', 'Stores');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page">
    <div class="page__header">
        <h1 class="page__title"><?= $this->title; ?></h1>
        <?php if (!UserHelper::isOperator()) : ?>
            <a class="btn btn--primary js-view-modal" href="<?= Url::to(['create']) ?>"><?= Yii::t('app', 'Create')?></a>
        <?php endif; ?>
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
                'options' => ['width' => 250],
                'value' => function (Store $model) {
                    $action = 'update';
                    if (UserHelper::isOperator()){
                        $action = 'view';
                    }
                    return Html::a($model->name, [$action, 'id' => $model->id], ['class' => 'js-view-modal', 'data-pjax' => 0]);
                },
            ],
            [
                'attribute' => 'name_short',
                'options' => ['width' => 250],
                'value' => function (Store $model) {
                    return StoreHelper::getNameShort($model);
                },
            ],
            [
                'attribute' => 'number',
                'options' => ['width' => 150]
            ],
            [
                'attribute' => 'city_id',
                'options' => ['width' => 150],
                'value' => function (Store $model) {
                    return $model->city ? $model->city->name : '';
                },
                'filter' => CityHelper::getSelectArray()
            ],
            [
                'attribute' => 'address'
            ],
            [
                'attribute' => 'type',
                'options' => ['width' => 150],
                'filter' => StoreHelper::getTypeArray(),
                'value' => function (Store $model) {
                    return StoreHelper::getTypeName($model->type);
                },
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'options' => ['width' => 200],
                'value' => function (Store $model) {
                    return StoreHelper::getStatusLabel($model->status);
                },
                'filter' => StoreHelper::getStatusArray()
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
