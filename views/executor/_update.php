<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\forms\ExecutorUpdateForm;
use conquer\select2\Select2Widget;

/* @var $this View */
/* @var $model ExecutorUpdateForm */

?>
<div class="modal__container executor-profile-modal">
    <div class="modal__title">
        <span class="executor-profile-modal__eyebrow">Профиль исполнителя</span>
        <span class="executor-profile-modal__name"><?= Html::encode($model->getExecutorName()) ?></span>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'validateOnChange' => false,
    ]); ?>
    <div class="modal__body">
        <div class="modal-form">
            <div class="modal-form__row">
                <?= $form->field($model, 'location_id')->widget(Select2Widget::class, [
                    'items' => ['' => ''] + $model->getLocationOptions(),
                    'placeholder' => 'Не указана',
                    'bootstrap' => false,
                    'settings' => [
                        'allowClear' => true,
                        'width' => '100%',
                    ],
                    'options' => [
                        'class' => 'form-control executor-location-select',
                    ],
                ]) ?>
            </div>
            <div class="modal-form__row">
                <?= $form->field($model, 'service_types')->checkboxList(
                    $model->getServiceOptions(),
                    [
                        'class' => 'executor-services-edit',
                        'item' => static function (
                            int $index,
                            string $label,
                            string $name,
                            bool $checked,
                            string $value
                        ): string {
                            $input = Html::checkbox($name, $checked, [
                                'value' => $value,
                                'class' => 'executor-service-card__input',
                            ]);
                            $content = Html::tag(
                                'span',
                                Html::tag('span', '', [
                                    'class' => 'executor-service-card__mark',
                                    'aria-hidden' => 'true',
                                ]) .
                                Html::tag(
                                    'span',
                                    Html::encode($label),
                                    ['class' => 'executor-service-card__name']
                                ),
                                ['class' => 'executor-service-card__content']
                            );

                            return Html::label(
                                $input . $content,
                                null,
                                ['class' => 'executor-service-card']
                            );
                        },
                    ]
                ) ?>
            </div>
        </div>
    </div>
    <div class="modal__footer">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()">
            <?= Yii::t('app', 'Close') ?>
        </a>
        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn--success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <i class="modal__close icon-close"></i>
</div>
