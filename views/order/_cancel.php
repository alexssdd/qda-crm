<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\forms\order\OrderCancelForm;
use app\core\helpers\OrderReasonHelper;

/* @var $this View */
/* @var $model OrderCancelForm */

$allReasons = OrderReasonHelper::getReasonsCancel();
$reasons = [];
foreach ($allReasons as $item){
    $reasons[$item['name']] = $item['name'];
}

?>
<div class="modal__container">
    <div class="modal__title">Отмена заказа</div>
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'validateOnChange' => false,
    ]); ?>
    <div class="modal__body">
        <div class="modal-form">
            <?= $form->field($model, 'reason')->dropDownList($reasons, [
                'prompt' => Yii::t('app', 'Select value')
            ]) ?>
            <?= $form->field($model, 'reason_additional', [
                'options' => ['class' => 'order-cancel-reason__additional']
            ])->dropDownList([]) ?>
        </div>
    </div>
    <div class="modal__footer">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
        <?= Html::submitButton('Отменить', ['class' => 'btn btn--warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <i class="modal__close icon-close"></i>
</div>
<?php

$allReasons = ArrayHelper::index($allReasons, 'name');
$allReasonsJson = Json::encode($allReasons);

$js = <<<JS

Order.cancelReasons = $allReasonsJson;

JS;

$this->registerJs($js);

?>