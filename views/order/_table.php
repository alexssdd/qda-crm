<?php

use yii\helpers\Html;
use app\widgets\GridView;
use app\search\OrderSearch;
use yii\widgets\MaskedInput;
use yii\data\ArrayDataProvider;
use app\core\helpers\PhoneHelper;
use app\modules\order\models\Order;
use app\modules\order\helpers\OrderHelper;
use app\modules\order\helpers\OrderHistoryHelper;

/** @var $searchModel OrderSearch */
/** @var $dataProvider ArrayDataProvider */

?>
<?= GridView::widget([
    'id' => 'order-grid-view',
    'dataProvider' => $dataProvider,
    'layout' => "{items}\n{pager}",
    'filterModel' => $searchModel,
    'filterSelector' => '.order-filter__status, .order-filter__date, .order-filter__id, .order-filter__my, .modal-filter input[type=hidden]',
    'options' => ['class' => 'order-grid'],
    'tableOptions' => ['class' => 'order-table', 'id' => 'order-table'],
    'rowOptions'=> function ($model) use ($searchModel) {
        if ($searchModel->id == $model['id']){
            return ['class' => 'order-table__tr--active'];
        }

        return [];
    },
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'options' => ['width' => 35]
        ],
        [
            'attribute' => 'number',
            'label' => 'Номер',
            'format' => 'raw',
            'options' => ['width' => 105],
            'value' => function (Order $model) {
                $result = $model->id;

                return Html::tag('span', $result, ['class' => 'order-table__number']);
            }
        ],
        [
            'attribute' => 'type',
            'label' => 'Тип',
            'format' => 'raw',
            'options' => ['width' => 105],
            'filter' => OrderHelper::getTypes(),
            'value' => function (Order $model) {
                return OrderHelper::getTypeName($model->type);
            }
        ],
        [
            'attribute' => 'name',
            'label' => 'Клиент',
            'format' => 'raw',
            'value' => function (Order $model) {
                $star = Html::tag('span', '', ['class' => 'icon-star order-table__star order-table__star--green']);
                return $model->name;
            }
        ],
        [
            'attribute' => 'phone',
            'label' => 'Телефон',
            'options' => ['width' => 125],
            'filter' => MaskedInput::widget([
                'model' => $searchModel,
                'name' => 'phone',
                'value' => $searchModel->phone,
                'mask' => '+7(999)999-99-99',
                'options' => ['class' => 'form-control order-table__phone']
            ]),
            'value' => function (Order $model) {
                return PhoneHelper::getMaskPhone($model->phone);
            }
        ],
        [
            'attribute' => 'country_code',
            'label' => 'Страна',
            'options' => ['width' => 120],
            'filter' => OrderHelper::getCountries(),
            'value' => function (Order $model) {
                return $model->country ? $model->country->name : null;
            }
        ],
        [
            'attribute' => 'channel',
            'label' => 'Канал',
            'options' => ['width' => 100],
            'filter' => OrderHelper::getChannels(),
            'value' => function (Order $order) {
                return OrderHelper::getChannel($order->channel);
            }
        ],
        [
            'attribute' => 'cost',
            'label' => 'Сумма',
            'options' => ['width' => 85],
            'value' => function (Order $model) {
                return OrderHelper::getPriceLabel($model);
            }
        ],
        [
            'options' => ['width' => 110],
            'format' => 'raw',
            'value' => function (Order $model) {
                $result = '';
                $steps = OrderHistoryHelper::getSteps($model);
                foreach ($steps as $step) {
                    if ($step){
                        $result .= Html::tag('span', '', [
                            'title' => $step['diff'],
                            'class' => 'order-step__item order-step__item--' . $step['status']
                        ]);
                    } elseif (!OrderHelper::isCompleted($model->status)) {
                        $result .= Html::tag('span', '', ['class' => 'order-step__item']);
                    }
                }

                return Html::tag('span', $result, [
                    'class' => 'order-step'
                ]);
            }
        ],
    ],
]) ?>