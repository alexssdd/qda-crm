<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\widgets\GridView;
use app\search\UserSearch;
use yii\widgets\MaskedInput;
use yii\data\ActiveDataProvider;
use app\core\helpers\PhoneHelper;
use app\modules\auth\models\User;
use app\modules\auth\helpers\UserHelper;

/* @var $this View */
/* @var $searchModel UserSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Employees');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page">
    <div class="page__header">
        <h1 class="page__title"><?= $this->title; ?></h1>
        <a class="btn btn--primary js-view-modal" href="<?= Url::to(['create']) ?>"><?= Yii::t('app', 'Create')?></a>
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
                'value' => function (User $model) {
                    return Html::a(Html::encode($model->name), ['update', 'id' => $model->id], [
                        'class' => 'js-view-modal',
                        'data-pjax' => 0,
                    ]);
                },
            ],
            [
                'attribute' => 'role',
                'options' => ['width' => 200],
                'value' => function (User $model) {
                    return UserHelper::getRoleName($model->role);
                },
                'filter' => UserHelper::getRoleArray()
            ],
            [
                'attribute' => 'phone',
                'options' => ['width' => 200],
                'value' => function (User $model) {
                    return PhoneHelper::getMaskPhone($model->phone);
                },
                'filter' => MaskedInput::widget([
                    'model' => $searchModel,
                    'name' => 's[phone]',
                    'value' => $searchModel->phone,
                    'mask'=> '+7(999)999-99-99',
                ])
            ],
            [
                'attribute' => 'country',
                'options' => ['width' => 120],
                'value' => static fn(User $model): string => mb_strtoupper($model->country),
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
                'value' => function (User $model) {
                    return UserHelper::getStatusLabel($model->status);
                },
                'filter' => UserHelper::getStatusArray()
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
