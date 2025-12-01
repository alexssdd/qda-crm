<?php

use yii\web\View;
use yii\widgets\Pjax;
use app\assets\CartAsset;
use app\assets\OrderAsset;
use app\search\OrderSearch;
use yii\data\ArrayDataProvider;

/* @var $order [] */
/* @var $this View */
/* @var $searchModel OrderSearch */
/* @var $dataProvider ArrayDataProvider */

// Assets
OrderAsset::register($this);
CartAsset::register($this);

// View params
$this->title = Yii::t('app', 'Orders');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page orders">
    <?= $this->render('_filter', [
        'searchModel' => $searchModel
    ]) ?>
    <?php Pjax::begin([
        'options' => ['class' => 'orders__body']
    ]) ?>
    <div class="orders__left">
        <?= $this->render('_table', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]) ?>
    </div>
    <div class="orders__right">
        <?php if ($order) : ?>
            <div class="order">
                <?= $this->render('detail/top', ['order' => $order]) ?>
                <div class="order-detail">
                    <div class="order__left">
                        <?= $this->render('detail/header', ['order' => $order]) ?>
                        <?= $this->render('detail/body', ['order' => $order]) ?>
                    </div>
                    <div class="order__right">
                        <?= $this->render('detail/history', ['order' => $order]) ?>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="order">
                <div class="order-top"></div>
                <div class="order-detail">
                    <div class="order__left">
                        <div class="order-header"></div>
                        <div class="order-body"></div>
                    </div>
                    <div class="order__right">
                        <div class="order-history"></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php Pjax::end() ?>
</div>