<?php

use yii\web\View;
use yii\helpers\Html;
use app\widgets\Alert;
use app\assets\EmptyAsset;

/* @var $this View */
/* @var $content string */

EmptyAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Html::encode(Yii::$app->language) ?>">
<head>
    <meta charset="<?= Html::encode(Yii::$app->charset) ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode(Yii::$app->name) . ' &mdash; ' . Html::encode($this->title) ?></title>
    <?= $this->render('_favicons') ?>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?= Alert::widget() ?>
<?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
