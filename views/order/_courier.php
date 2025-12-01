<?php

use yii\web\View;
use yii\helpers\Json;
use app\entities\Order;
use yii\widgets\ActiveForm;
use app\core\helpers\OrderHelper;

/* @var $this View */
/* @var $order Order */

// Variables
$courier = $order->courier;
$data = OrderHelper::getMapData($order);
$arrivedAt = $courier->getArrivedAt();

?>
<div class="modal__container modal__container--1200 order-courier">
    <div class="modal__title">Маршрут</div>
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form'
    ]); ?>
    <div class="modal__body">
        <div class="order-courier__row">
            <div class="order-courier__left">
                <div class="modal-form">
                    <?php foreach ($data['stores'] as $store) : ?>
                        <div class="form-group">
                            <label class="control-label"><?= $store['name'] ?></label>
                            <input type="text" class="form-control" value="<?= $store['address'] ?>" readonly>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <?= $form->field($order, 'address')->textInput(['readonly' => true])->label(Yii::t('app', 'Customer Address')) ?>
                    <hr>
                    <?= $form->field($courier, 'name')->textInput(['readonly' => true]) ?>
                    <?= $form->field($courier, 'phone')->textInput(['readonly' => true]) ?>
                    <?php if ($arrivedAt) : ?>
                        <div class="form-group">
                            <label class="control-label">Дата доставки</label>
                            <input type="text" class="form-control" value="<?= Yii::$app->formatter->asDatetime($arrivedAt) ?>" readonly>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="order-courier__right">
                <div class="order-courier__map" id="orderCourierMap"></div>
            </div>
        </div>
    </div>
    <div class="modal__footer">
        <a href="#" class="modal__form_close btn btn--default" onclick="OrderCourier.close()"><?= Yii::t('app', 'Close'); ?></a>
        <button type="button" class="btn btn--success" onclick="OrderCourier.loadCourierCoordinates()">Координаты курьера</button>
    </div>
    <i class="modal__close icon-close"></i>
    <?php ActiveForm::end() ?>
</div>
<?php

$city = $order->city;
$store = $order->city;

$params = Json::encode($data);

$js = <<<JS

OrderCourier.init($params);

JS;

$this->registerJs($js);

?>