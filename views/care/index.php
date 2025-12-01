<?php

use yii\web\View;
use yii\widgets\Pjax;
use app\assets\CareAsset;
use app\search\CareSearch;
use app\assets\AppealAsset;
use yii\data\ArrayDataProvider;

/* @var $care [] */
/* @var $this View */
/* @var $searchModel CareSearch */
/* @var $dataProvider ArrayDataProvider */

// Assets
CareAsset::register($this);
AppealAsset::register($this);

// View params
$this->title = Yii::t('app', 'Cares');

?>
<div class="page cares">
    <?= $this->render('_filter', [
        'searchModel' => $searchModel
    ]) ?>
    <?php Pjax::begin([
        'options' => ['class' => 'cares__body']
    ]) ?>
    <div class="cares__left">
        <?= $this->render('_table', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]) ?>
    </div>
    <div class="cares__right">
        <?php if ($care) : ?>
            <div class="care">
                <?= $this->render('detail/top', ['care' => $care]) ?>
                <div class="care-detail">
                    <div class="care__left">
                        <?= $this->render('detail/header', ['care' => $care]) ?>
                        <?= $this->render('detail/body', ['care' => $care]) ?>
                    </div>
                    <div class="care__right">
                        <?= $this->render('detail/history', ['care' => $care]) ?>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="care">
                <div class="care-top"></div>
                <div class="care-detail">
                    <div class="care__left">
                        <div class="care-header"></div>
                        <div class="care-body"></div>
                    </div>
                    <div class="care__right">
                        <div class="care-history"></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php Pjax::end() ?>
</div>