<?php

use yii\web\View;
use yii\helpers\Html;

/* @var $this View */
/* @var $shareUrl string|null */

?>
<div class="modal__container modal__container--500">
    <div class="modal__title">Поделиться заказом</div>
    <div class="modal__body">
        <?php if ($shareUrl): ?>
            <p class="order-share__hint">Отправьте ссылку исполнителю — мессенджер развернёт её в карточку заказа:</p>
            <pre class="order-share__link"><?= Html::encode($shareUrl) ?></pre>
            <div class="order-share__actions">
                <a class="btn btn--default" target="_blank" rel="noopener"
                   href="https://wa.me/?text=<?= rawurlencode($shareUrl) ?>">WhatsApp</a>
                <a class="btn btn--default" target="_blank" rel="noopener"
                   href="https://t.me/share/url?url=<?= rawurlencode($shareUrl) ?>">Telegram</a>
                <a href="#" class="btn btn--default js-share-copy" data-url="<?= Html::encode($shareUrl) ?>">
                    <svg class="order-share__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                    </svg>
                    <span class="js-share-copy-label">Скопировать ссылку</span>
                </a>
            </div>
        <?php else: ?>
            <p>У заказа ещё нет публичной ссылки.</p>
        <?php endif; ?>
    </div>
    <div class="modal__footer">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()">Закрыть</a>
    </div>
    <i class="modal__close icon-close"></i>
</div>
