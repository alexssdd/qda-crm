<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Menu;
use app\core\helpers\UserHelper;

/* @var $this View */

// Variables
$user = UserHelper::getIdentity();
$isAdmin = UserHelper::isAdmin();
$canManageEmployees = $isAdmin || UserHelper::isAdministrator();
$isOperator = false;
$isOnline = false

?>
<header class="header">
    <div class="header__left">
        <a href="<?= Url::to(['/order/index']) ?>" class="header-logo">GoQDA</a>
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
                    'label' => '<i class="header-menu__icon icon-local_grocery_store" aria-hidden="true"></i>',
                    'url' => ['/order/index'],
                    'template' => '<a class="header-menu__link" href="{url}" title="Заказы" aria-label="Заказы">{label}</a>',
                ],
                [
                    'label' => '<i class="header-menu__icon icon-storage" aria-hidden="true"></i>',
                    'url' => '#',
                    'visible' => $isAdmin,
                    'template' => '<a class="header-menu__link" href="{url}" title="Справочники" aria-label="Справочники">{label}</a>',
                    'items' => [
                        ['label' => 'Страны', 'url' => ['/country/index'], 'visible' => $isAdmin],
                        ['label' => 'Локации', 'url' => ['/city/index'], 'visible' => $isAdmin],
                    ]
                ],
                [
                    'label' => '<i class="header-menu__icon icon-more_vert" aria-hidden="true"></i>',
                    'url' => '#',
                    'template' => '<a class="header-menu__link" href="{url}" title="Ещё" aria-label="Ещё">{label}</a>',
                    'items' => [
                        [
                            'label' => 'Сотрудники',
                            'url' => ['/user/index'],
                            'visible' => $canManageEmployees,
                        ],
                        [
                            'label' => 'Клиенты', 'url' => ['/customer/index'],
                        ],
                        [
                            'label' => 'Исполнители', 'url' => ['/executor/index'],
                        ],
                        [
                            'label' => 'Журнал', 'url' => ['/log/index'], 'visible' => $isAdmin,
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
            <button
                type="button"
                class="header-theme-toggle js-theme-toggle"
                aria-label="Включить тёмную тему"
                aria-pressed="false"
                title="Включить тёмную тему"
            >
                <svg class="header-theme-toggle__icon header-theme-toggle__icon--moon" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M20.7 15.4A8.5 8.5 0 0 1 8.6 3.3 9 9 0 1 0 20.7 15.4Z"></path>
                </svg>
                <svg class="header-theme-toggle__icon header-theme-toggle__icon--sun" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2V1M12 23v-1M4.9 4.9l-.7-.7m15.6 15.6-.7-.7M2 12H1m22 0h-1M4.9 19.1l-.7.7M19.8 4.2l-.7.7"></path>
                </svg>
            </button>
            <a href="<?= Url::to(['/site/demo']) ?>" class="header-actions__item" title="Уведомления" aria-label="Уведомления">
                <i class="header-actions__icon icon-notifications" aria-hidden="true"></i>
            </a>
        </div>
        <div class="header-user">
            <button type="button" class="header-user__info" onclick="Header.userBlockToggle()" title="Меню пользователя" aria-label="Открыть меню пользователя">
                <span class="header-user__name"><?= Html::encode($user->name) ?></span>
                <i class="header-user__arrow icon-keyboard_arrow_down" aria-hidden="true"></i>
                <i class="header-user__icon icon-person" aria-hidden="true"></i>
            </button>
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
