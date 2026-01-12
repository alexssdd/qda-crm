<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Menu;
use app\core\helpers\UserHelper;

/* @var $this View */

// Variables
$user = UserHelper::getIdentity();
$isOperator = false;
$isOnline = false

?>
<header class="header">
    <div class="header__left">
        <a href="<?= Url::to(['/site/index']) ?>" class="header-logo">
            <img class="header-logo__image" src="/images/logo.png" alt="Company logo">
        </a>
    </div>
    <div class="header__middle">
        <?= Menu::widget([
            'options' => ['class' => 'header-menu'],
            'itemOptions' => ['class' => 'header-menu__item'],
            'activeCssClass' => 'header-menu__item--active',
            'linkTemplate' => '<a class="header-menu__link" href="{url}">{label}</a>',
            'submenuTemplate' => '<ul class="header-menu__sub">{items}</ul>',
            'encodeLabels' => false,
            'items' => [
                [
                    'label' => '<i class="header-menu__icon icon-local_grocery_store"></i>',
                    'url' => ['/order/index'],
                ],
                [
                    'label' => '<i class="header-menu__icon icon-storage"></i>',
                    'url' => '#',
                    'items' => [
                        ['label' => 'Countries', 'url' => ['/country/index']],
                        ['label' => 'Locations', 'url' => ['/location/index']],
                    ]
                ],
                [
                    'label' => '<i class="header-menu__icon icon-more_vert"></i>',
                    'url' => '#',
                    'items' => [
                        [
                            'label' => 'Users', 'url' => ['/user/index'],
                        ],
                        [
                            'label' => 'Customers', 'url' => ['/customer/index'],
                        ],
                        [
                            'label' => 'Executors', 'url' => ['/executor/index'],
                        ],
                        [
                            'label' => 'Logs', 'url' => ['/log/index'],
                        ],
                    ]
                ],
            ]
        ]) ?>
    </div>
    <div class="header__right">
        <?php if ($isOperator) : ?>
        <div class="header-state header-state--<?= $isOnline ? 'online' : 'offline' ?>" data-online="Закрыть смену" data-offline="Открыть смену">
            <?= Html::tag('div', $isOnline ? 'Закрыть смену' : 'Открыть смену', [
                'class' => 'header-state__button transition',
                'onclick' => 'Header.userState(\'' . ($isOnline ? 'offline' : 'online') . '\')',
                'encodeSpaces' => false
            ]) ?>
        </div>
        <?php endif; ?>
        <div class="header-actions">
            <a href="<?= Url::to(['/site/demo']) ?>" class="header-actions__item">
                <i class="header-actions__icon icon-notifications"></i>
            </a>
        </div>
        <div class="header-user">
            <div class="header-user__info" onclick="Header.userBlockToggle()">
                <div class="header-user__name"><?= $user->name ?></div>
                <i class="header-user__arrow icon-keyboard_arrow_down"></i>
                <i class="header-user__icon icon-person"></i>
            </div>
            <div class="header-user__block">
                <ul class="header-user-menu">
                    <li class="header-user-menu__item">
                        <a class="header-user-menu__link" href="<?= Url::to(['/site/demo']) ?>">
                            <i class="header-user-menu__icon icon-settings"></i>
                            <?= Yii::t('app', 'Settings') ?>
                        </a>
                    </li>
                    <li class="header-user-menu__item">
                        <a class="header-user-menu__link" href="<?= Url::to(['site/logout']) ?>" data-method="post">
                            <i class="header-user-menu__icon icon-logout"></i>
                            <?= Yii::t('app', 'Logout') ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
