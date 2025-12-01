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
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Yii::$app->name . ' &mdash; ' . Html::encode($this->title) ?></title>
    <link rel="icon" type="image/x-icon" href="/favicons/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="/favicons/favicon.ico">
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
