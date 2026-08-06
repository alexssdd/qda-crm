<?php

use yii\web\View;
use yii\helpers\Html;

/* @var $this View */
/* @var $preview string|null */
/* @var $shareUrl string|null */

?>
<div class="modal__container modal__container--500">
    <div class="modal__title">Поделиться заказом</div>
    <div class="modal__body">
        <?php if ($shareUrl): ?>
            <p class="order-share__hint">Так заказ увидит исполнитель — мессенджер развернёт ссылку в карточку:</p>
            <pre class="order-share__preview"><?= Html::encode($preview) ?></pre>
            <pre class="order-share__link"><?= Html::encode($shareUrl) ?></pre>
            <div class="order-share__actions">
                <a class="btn btn--default" target="_blank" rel="noopener"
                   href="https://wa.me/?text=<?= rawurlencode($shareUrl) ?>">WhatsApp</a>
                <a class="btn btn--default" target="_blank" rel="noopener"
                   href="https://t.me/share/url?url=<?= rawurlencode($shareUrl) ?>">Telegram</a>
                <a href="#" class="btn btn--default js-share-copy" data-message="<?= Html::encode($shareUrl) ?>">Скопировать ссылку</a>
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
