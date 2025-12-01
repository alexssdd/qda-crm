<?php

use yii\web\View;
use app\assets\LeadAsset;
use app\search\LeadSearch;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $result [] */
/* @var $searchModel LeadSearch */

// Assets
LeadAsset::register($this);

// View params
$this->title = Yii::t('app', 'Leads');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page leads">
    <?php $form = ActiveForm::begin([
        'method' => 'GET',
        'action' => ['/lead/index'],
        'id' => 'lead-form'
    ]) ?>
    <?= $this->render('_header', [
        'searchModel' => $searchModel
    ]) ?>
    <?= $this->render('_columns', [
        'result' => $result
    ]) ?>
    <?php ActiveForm::end() ?>
    <div class="lead-back transition" onclick="Lead.modalClose()"></div>
    <div class="lead-modal transition"></div>
</div>