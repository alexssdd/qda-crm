<?php

/* @var $this yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\assets\ThemeAsset;

AppAsset::register($this);
ThemeAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Html::encode(Yii::$app->language) ?>">
<head>
    <meta charset="<?= Html::encode(Yii::$app->charset) ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        (function () {
            var theme = null;

            try {
                theme = window.localStorage.getItem('qda-theme');
            } catch (error) {
                // The theme can still follow the operating system when storage is unavailable.
            }

            if (theme !== 'light' && theme !== 'dark') {
                theme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
                    ? 'dark'
                    : 'light';
            }

            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.style.colorScheme = theme;
        })();
    </script>
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?= $this->render('_favicons') ?>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?= $this->render('_header')?>
<main class="main">
    <?= Alert::widget() ?>
    <?= $content ?>
</main>
<?= $this->render('_footer') ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
