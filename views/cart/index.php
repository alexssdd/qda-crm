<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\MaskedInput;
use app\core\helpers\CartHelper;
use app\core\helpers\CityHelper;
use conquer\select2\Select2Widget;
use app\forms\cart\CartCreateForm;

/* @var $this View */
/* @var $products string */
/* @var $model CartCreateForm */

// Variables
$cartParams = CartHelper::getParams();
$deliveryParams = CartHelper::getDeliveryParams();
$merchants = ArrayHelper::map($cartParams['merchants'], 'id', 'value');
$deliveryMethods = ArrayHelper::map($cartParams['delivery_methods'], 'id', 'value');
$paymentMethods = ArrayHelper::map($cartParams['payment_methods'], 'id', 'value');

?>
<div class="modal__container modal__container--1000">
    <div class="modal__title">Новый заказ</div>
    <?php $form = ActiveForm::begin([
        'id' => 'cart-form',
        'fieldConfig' => [
            'template' => "{input}\n{hint}"
        ]
    ]) ?>
    <div class="modal__body">
        <div class="cart">
            <div class="cart-header">
                <div class="cart-search">
                    <input type="text" class="cart-search__input" placeholder="Введите название товара">
                    <i class="cart-search__icon icon-search"></i>
                </div>
                <div class="cart-header__item">
                    <?= $form->field($model, 'merchant_id')->dropDownList($merchants) ?>
                </div>
                <div class="cart-header__item">
                    <?= $form->field($model, 'city_id')->widget(Select2Widget::class, [
                        'items' => CityHelper::getSelectArray(),
                        'bootstrap' => false
                    ]) ?>
                </div>
            </div>
            <div class="cart-products">
                <table class="modal-table modal-table--striped">
                    <thead>
                        <tr>
                            <th class="modal-table__td--35 text-center">№</th>
                            <th class="modal-table__td--85 text-center">SKU</th>
                            <th class="modal-table__td--527">Товар</th>
                            <th class="modal-table__td--150">Бренд</th>
                            <th class="modal-table__td--85 text-center">Остаток</th>
                            <th class="modal-table__td--87 text-center">Цена</th>
                        </tr>
                    </thead>
                    <tbody><?= $products ?></tbody>
                </table>
                <div class="cart-products__loader">
                    <div class="cart-products__loader-circle"></div>
                </div>
            </div>
            <div class="cart-actions">
                <button type="button" class="cart-actions__button btn btn--primary" disabled onclick="Cart.actionAddItem()">Добавить в корзину</button>
                <button type="button" class="cart-actions__button btn btn--default" disabled onclick="Cart.actionStockOnline()">Остаток (онлайн)</button>
                <button type="button" class="cart-actions__button btn btn--default" disabled onclick="Cart.actionStockCity()">Остаток (города)</button>
                <button type="button" class="cart-actions__button btn btn--default" disabled onclick="Cart.actionDefectura()">Дефектура</button>
            </div>
            <div class="cart-body">
                <div class="cart-body__left">
                    <div class="cart-form">
                        <div class="cart-form__items">
                            <div class="cart-form__item required">
                                <label class="cart-form__label"><?= $model->getAttributeLabel('phone') ?></label>
                                <div class="cart-form__block cart-form__block--group">
                                    <?= $form->field($model, 'phone')->widget(MaskedInput::class, [
                                        'mask' => '+7(999)999-99-99',
                                        'options' => [
                                            'class' => 'form-control',
                                            'autocomplete' => 'off'
                                        ]
                                    ]) ?>
                                    <?= $form->field($model, 'phone_ext')->widget(MaskedInput::class, [
                                        'mask' => '+7(999)999-99-99',
                                        'options' => [
                                            'class' => 'form-control',
                                            'autocomplete' => 'off'
                                        ]
                                    ]) ?>
                                </div>
                            </div>
                            <div class="cart-form__item required">
                                <label class="cart-form__label"><?= $model->getAttributeLabel('customer_id') ?></label>
                                <div class="cart-form__block">
                                    <?= $form->field($model, 'name') ?>
                                </div>
                            </div>
                            <div class="cart-form__item required">
                                <label class="cart-form__label"><?= $model->getAttributeLabel('delivery_method') ?></label>
                                <div class="cart-form__block">
                                    <?= $form->field($model, 'delivery_method')->dropDownList($deliveryMethods, [
                                        'prompt' => ''
                                    ]) ?>
                                </div>
                            </div>
                            <div class="cart-form__item required cart-form__address">
                                <label class="cart-form__label"><?= $model->getAttributeLabel('address') ?></label>
                                <div class="cart-form__block">
                                    <?= $form->field($model, 'address', [
                                        'template' => '<div class="cart-form__address-block"></div>{input}'
                                    ])->hiddenInput() ?>
                                    <a href="#" class="cart-form__link" onclick="Cart.addressSelect()">Указать адрес</a>
                                </div>
                            </div>
                            <div class="cart-form__item cart-form__store">
                                <label class="cart-form__label"><?= $model->getAttributeLabel('store_id') ?></label>
                                <div class="cart-form__block">
                                    <?= $form->field($model, 'store_id', [
                                        'template' => '<div class="cart-form__store-block"></div>{input}'
                                    ])->hiddenInput() ?>
                                    <a href="#" class="cart-form__link" onclick="Cart.storeSelect()">Выбрать точку продажи</a>
                                </div>
                            </div>
                            <div class="cart-form__item required">
                                <label class="cart-form__label"><?= $model->getAttributeLabel('payment_method') ?></label>
                                <div class="cart-form__block">
                                    <?= $form->field($model, 'payment_method')->dropDownList([]) ?>
                                </div>
                            </div>
                            <div class="cart-form__item">
                                <label class="cart-form__label"><?= $model->getAttributeLabel('comment') ?></label>
                                <div class="cart-form__block">
                                    <?= $form->field($model, 'comment') ?>
                                </div>
                            </div>
                        </div>
                        <div class="d-none">
                            <?= $form->field($model, 'customer_id')->hiddenInput() ?>
                            <?= $form->field($model, 'lat')->hiddenInput() ?>
                            <?= $form->field($model, 'lng')->hiddenInput() ?>
                            <?= $form->field($model, 'house')->hiddenInput() ?>
                            <?= $form->field($model, 'apartment')->hiddenInput() ?>
                            <?= $form->field($model, 'intercom')->hiddenInput() ?>
                            <?= $form->field($model, 'entrance')->hiddenInput() ?>
                            <?= $form->field($model, 'floor')->hiddenInput() ?>
                            <?= $form->field($model, 'address_type')->hiddenInput() ?>
                            <?= $form->field($model, 'address_title')->hiddenInput() ?>
                            <?= $form->field($model, 'delivery_cost')->hiddenInput() ?>
                            <?= $form->field($model, 'lead_id')->hiddenInput() ?>
                        </div>
                    </div>
                </div>
                <div class="cart-body__right">
                    <div class="cart-items">
                        <table class="modal-table modal-table--sm modal-table--striped">
                            <thead>
                                <tr>
                                    <th class="modal-table__td--35 text-center">№</th>
                                    <th class="modal-table__td--290">Товар</th>
                                    <th class="modal-table__td--85 text-center">Кол-во</th>
                                    <th class="modal-table__td--85 text-center">Цена</th>
                                    <th class="modal-table__td--37"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <template id="templateCartItem">
                            <tr data-id="{id}" data-sku="{sku}">
                                <td class="modal-table__td--35 text-center cart-items__index"></td>
                                <td class="modal-table__td--290">{name}</td>
                                <td class="modal-table__td--85 text-center">
                                    <input type="number" name="Cart[products][{id}][quantity]" class="modal-table__input text-center cart-items__quantity" value="{quantity}" onchange="Cart.itemsChange(this)">
                                </td>
                                <td class="modal-table__td--85 text-center cart-items__price">{price}</td>
                                <td class="modal-table__td--35 text-center">
                                    <i class="modal-table__remove icon-close" onclick="Cart.itemsRemove({id})"></i>
                                </td>
                            </tr>
                        </template>
                    </div>
                    <div class="cart-total">
                        <div class="cart-loader">
                            <div class="cart-loader__circle"></div>
                            <div class="cart-loader__text"></div>
                        </div>
                        <div class="cart-total__item cart-total__item--cost">
                            Сумма заказа: <span class="cart-total__value">0</span> тг
                        </div>
                        <div class="cart-total__item cart-total__item--delivery">
                            Стоимость доставки: <span class="cart-total__value">0</span> тг
                        </div>
                        <div class="cart-total__item cart-total__item--total">
                            Итого: <span class="cart-total__value">0</span> тг
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal__footer modal__footer--bordered">
        <a href="#" class="modal__form_close btn btn--default" onclick="Modal.close()"><?= Yii::t('app', 'Close'); ?></a>
        <?= Html::submitButton('Создать', ['class' => 'btn btn--success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    <i class="modal__close icon-close"></i>
</div>
<?php

$cartParamsJson = Json::encode($cartParams);
$deliveryParamsJson = Json::encode($deliveryParams);

$js = <<<JS

Cart.cartParams = $cartParamsJson;
Cart.deliveryParams = $deliveryParamsJson;
Cart.init();

JS;

$this->registerJs($js);

?>