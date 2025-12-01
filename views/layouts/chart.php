<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\assets\ChartAsset;

/* @var $this View */
/* @var $content string */

ChartAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.svg">
    <link rel="shortcut icon" type="image/x-icon" href="/images/favicon.svg">
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
    <header class="header">
        <a href="<?= Url::home() ?>">
            <img src="/images/marwin.png" alt="Company logo" class="header__logo">
        </a>
        <div class="header__slogan"><?= $this->title ?></div>
    </header>
    <main class="main">
        <?= $content ?>
    </main>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
