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
            <img class="header-logo__image" src="/images/marwin.png" alt="Company logo">
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
                    'visible' => Yii::$app->user->can(UserHelper::ROLE_OPERATOR)
                ],
                [
                    'label' => '<i class="header-menu__icon icon-favorite"></i>',
                    'url' => ['/care/index'],
                    'visible' => Yii::$app->user->can(UserHelper::ROLE_OPERATOR)
                ],
                [
                    'label' => '<i class="header-menu__icon icon-analytics"></i>',
                    'url' => '#',
                    'visible' => Yii::$app->user->can(UserHelper::ROLE_OPERATOR),
                    'items' => [
                        ['label' => 'Показатели', 'url' => ['/chart/index']],
                        ['label' => 'Мониторинг', 'url' => ['/chart/operator']],
                        ['label' => 'Остатки по каналам', 'url' => ['/dashboard/channel-export']],
                        ['label' => 'Топы продаж и их остатки', 'url' => ['/dashboard/channel-product']],
                    ]
                ],
                [
                    'label' => '<i class="header-menu__icon icon-library_books"></i>',
                    'url' => '#',
                    'visible' => Yii::$app->user->can(UserHelper::ROLE_ADMINISTRATOR),
                    'items' => [
                        ['label' => Yii::t('app', 'Report orders'), 'url' => ['/report/order']],
                        ['label' => Yii::t('app', 'Report defectura'), 'url' => ['/report/defectura']],
                        ['label' => Yii::t('app', 'Report cares'), 'url' => ['/report/care']],
                        ['label' => Yii::t('app', 'Report leads'), 'url' => ['/report/lead']],
                    ]
                ],
                [
                    'label' => '<i class="header-menu__icon icon-storage"></i>',
                    'url' => '#',
                    'visible' => Yii::$app->user->can(UserHelper::ROLE_OPERATOR),
                    'items' => [
                        ['label' => Yii::t('app', 'Brands'), 'url' => ['/brand/index']],
                        ['label' => Yii::t('app', 'Products'), 'url' => ['/product/index']],
                        ['label' => Yii::t('app', 'Stores'), 'url' => ['/store/index']],
                        ['label' => Yii::t('app', 'Customers'), 'url' => ['/customer/index']],
                    ]
                ],
                [
                    'label' => '<i class="header-menu__icon icon-more_vert"></i>',
                    'url' => '#',
                    'visible' => Yii::$app->user->can(UserHelper::ROLE_MARKETING),
                    'items' => [
                        [
                            'label' => Yii::t('app', 'Users'), 'url' => ['/user/index'],
                            'visible' => Yii::$app->user->can(UserHelper::ROLE_ADMINISTRATOR)
                        ],
                        [
                            'label' => Yii::t('app', 'Countries'), 'url' => ['/country/index'],
                            'visible' => Yii::$app->user->can(UserHelper::ROLE_ADMIN)
                        ],
                        [
                            'label' => Yii::t('app', 'Cities'), 'url' => ['/city/index'],
                            'visible' => Yii::$app->user->can(UserHelper::ROLE_ADMIN)
                        ],
                        [
                            'label' => Yii::t('app', 'Merchants'), 'url' => ['/merchant/index'],
                            'visible' => Yii::$app->user->can(UserHelper::ROLE_ADMIN)
                        ],
                        [
                            'label' => Yii::t('app', 'Price types'), 'url' => ['/price-type/index'],
                            'visible' => Yii::$app->user->can(UserHelper::ROLE_ADMIN)
                        ],
                        [
                            'label' => Yii::t('app', 'Adverts'), 'url' => ['/advert/index'],
                            'visible' => Yii::$app->user->can(UserHelper::ROLE_MARKETING)
                        ],
                        [
                            'label' => Yii::t('app', 'Logs'), 'url' => ['/log/index'],
                            'visible' => Yii::$app->user->can(UserHelper::ROLE_ADMIN)
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
                        <a class="header-user-menu__link" href="<?= Url::to(['/site/logout']) ?>" data-method="post">
                            <i class="header-user-menu__icon icon-logout"></i>
                            <?= Yii::t('app', 'Logout') ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
