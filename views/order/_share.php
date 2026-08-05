<?php

use yii\web\View;
use yii\helpers\Html;

/* @var $this View */
/* @var $message string|null */
/* @var $messageWithoutUrl string|null */
/* @var $shareUrl string|null */

?>
<div class="modal__container modal__container--500">
    <div class="modal__title">Поделиться заказом</div>
    <div class="modal__body">
        <?php if ($message): ?>
            <pre class="order-share__preview"><?= Html::encode($message) ?></pre>
            <div class="order-share__actions">
                <a class="btn btn--default" target="_blank" rel="noopener"
                   href="https://wa.me/?text=<?= rawurlencode($message) ?>">WhatsApp</a>
                <a class="btn btn--default" target="_blank" rel="noopener"
                   href="https://t.me/share/url?url=<?= rawurlencode($shareUrl) ?>&text=<?= rawurlencode($messageWithoutUrl) ?>">Telegram</a>
                <a href="#" class="btn btn--default js-share-copy" data-message="<?= Html::encode($message) ?>">Скопировать</a>
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
