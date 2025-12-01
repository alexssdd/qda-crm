<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\core\helpers\CityHelper;
use app\forms\cart\CartDefecturaForm;

/* @var $this View */
/* @var $model CartDefecturaForm */

?>
<div class="modal__container">
    <div class="modal__title">Отмена заказа</div>
    <?php $form = ActiveForm::begin([
        'id' => 'defectura-form',
        'validateOnChange' => false,
    ]); ?>
    <div class="modal__body">
        <div class="modal-form">
            <?= $form->field($model, 'product_name')->textInput(['disabled' => true]) ?>
            <div class="modal-form__row">
                <?= $form->field($model, 'quantity')->input('number') ?>
                <?= $form->field($model, 'city_id')->dropDownList(CityHelper::getSelectArray(), [
                    'prompt' => Yii::t('app', 'Select value')
                ]) ?>
            </div>
        </div>
    </div>
    <div class="modal__footer">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.closeAdditional()"><?= Yii::t('app', 'Close'); ?></a>
        <?= Html::submitButton('Добавить дефектуру', ['class' => 'btn btn--success defectura-form__button']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <i class="modal__close icon-close"></i>
</div>
<?php

$js = <<<JS

Modal.ajaxForm('#defectura-form', function (res){
    // Error
    if (res['status'] === 'error'){
        alert(res['message']);
    }
});

JS;

$this->registerJs($js);

?>