<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\entities\Brand;
use app\widgets\GridView;
use app\search\BrandSearch;
use yii\data\ActiveDataProvider;
use app\core\helpers\UserHelper;
use app\core\helpers\BrandHelper;

/* @var $this View */
/* @var $searchModel BrandSearch */
/* @var $dataProvider ActiveDataProvider */

// View params
$this->title = Yii::t('app', 'Brands');
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
                'value' => function (Brand $model) {
                    $action = 'update';
                    if (UserHelper::isOperator()){
                        $action = 'view';
                    }
                    return Html::a($model->name, [$action, 'id' => $model->id], ['class' => 'js-view-modal', 'data-pjax' => 0]);
                },
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'options' => ['width' => 200],
                'value' => function (Brand $model) {
                    return BrandHelper::getStatusLabel($model->status);
                },
                'filter' => BrandHelper::getStatusArray()
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
