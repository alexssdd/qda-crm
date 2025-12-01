<?php

use yii\web\View;
use app\entities\Care;
use app\core\helpers\CareHelper;

/** @var $this View */
/** @var $care Care */
?>
<div class="care-header">
    <div class="care-header__left">
        <div class="care-header__items">
            <div class="care-header__item">
                <span class="care-header__label">Номер:</span>
                <span class="care-header__value"><?= $care->number ?></span>
            </div>
            <div class="care-header__item">
                <span class="care-header__label">Дата:</span>
                <span class="care-header__value"><?= CareHelper::getCreated($care)  ?></span>
            </div>
            <div class="care-header__item">
                <span class="care-header__label">Город:</span>
                <span class="care-header__value"><?= $care->city ? $care->city->name : '' ?></span>
            </div>
            <div class="care-header__item">
                <span class="care-header__label">Оператор:</span>
                <span class="care-header__value"><?= CareHelper::getHandlerName($care) ?></span>
            </div>
        </div>
    </div>
    <div class="care-header__right">
        <div class="care-header__items">
            <div class="care-header__item">
                <span class="care-header__label"><?= $care->getAttributeLabel('type') ?>:</span>
                <span class="care-header__value"><?= CareHelper::getTypeName($care->type) ?></span>
            </div>
            <div class="care-header__item">
                <span class="care-header__label"><?= $care->getAttributeLabel('rating') ?>:</span>
                <span class="care-header__value">
                    <?php if (!$care->rating) : ?>
                        Без оценки
                    <?php else : ?>
                        <span class="care-stars care-stars--<?= (int)$care->rating ?>">
                            <span class="care-star icon-star"></span>
                            <span class="care-star icon-star"></span>
                            <span class="care-star icon-star"></span>
                            <span class="care-star icon-star"></span>
                            <span class="care-star icon-star"></span>
                        </span>
                    <?php endif; ?>
                </span>
            </div>
        </div>
        <div class="care-header__time"></div>
    </div>
</div>
<?php

$seconds = time() - $care->created_at;
$stop = 'false';

if (CareHelper::isFinished($care->status)) {
    $stop = true;
    $seconds = $care->completed_at - $care->created_at;
}

$js = <<<JS

Care.id = $care->id;
Care.initTime($seconds, $stop);

JS;

$this->registerJs($js);

?>