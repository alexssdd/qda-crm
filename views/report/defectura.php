<?php

use yii\web\View;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\search\ReportSearch;
use yii\data\ActiveDataProvider;

/* @var $this View */
/* @var $searchModel ReportSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', 'Report defectura');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page">
    <div class="page__header">
        <h1 class="page__title"><?= $this->title; ?></h1>
        <a class="btn btn--primary js-view-modal" href="<?= Url::to(['/report/defectura-modal']) ?>"><?= Yii::t('app', 'Create report')?></a>
    </div>
    <?php Pjax::begin(); ?>
    <?= $this->render('_table', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]) ?>
    <?php Pjax::end(); ?>
</div>