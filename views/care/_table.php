<?php

use yii\helpers\Html;
use app\entities\Care;
use app\widgets\GridView;
use app\search\CareSearch;
use yii\widgets\MaskedInput;
use yii\data\ArrayDataProvider;
use app\core\helpers\CareHelper;
use app\core\helpers\TextHelper;
use app\core\helpers\CityHelper;
use app\core\helpers\PhoneHelper;
use app\core\helpers\CareHistoryHelper;

/** @var $searchModel CareSearch */
/** @var $dataProvider ArrayDataProvider */

?>
<?= GridView::widget([
    'id' => 'care-grid-view',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'filterSelector' => '.care-filter__status, .care-filter__date, .care-filter__id, .care-filter__my, .modal-filter input[type=hidden]',
    'options' => ['class' => 'care-grid'],
    'tableOptions' => ['class' => 'care-table', 'id' => 'care-table'],
    'layout' => "{items}\n<div class='grid-view__footer'>{pager}</div>",
    'rowOptions'=> function ($model) use ($searchModel) {
        if ($searchModel->id == $model['id']){
            return ['class' => 'care-table__tr--active'];
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
            'options' => ['width' => 105],
        ],
        [
            'attribute' => 'name',
            'label' => 'Клиент',
            'format' => 'raw',
            'value' => function (Care $model) {
                $star = Html::tag('span', '', ['class' => 'icon-star care-table__star care-table__star--green']);
                return Html::tag('span', $star . TextHelper::getShortName($model->name), [
                    'class' => 'care-table__customer'
                ]);
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
                'options' => ['class' => 'form-control care-table__phone']
            ]),
            'value' => function (Care $model) {
                return PhoneHelper::getMaskPhone($model->phone);
            }
        ],
        [
            'attribute' => 'city_id',
            'label' => 'Город',
            'options' => ['width' => 100],
            'filter' => CityHelper::getSelectArray(),
            'value' => function (Care $model) {
                return $model->city ? $model->city->name : '';
            }
        ],
        [
            'attribute' => 'channel',
            'options' => ['width' => 150],
            'filter' => CareHelper::getChannelArray(),
            'value' => function (Care $care) {
                return CareHelper::getChannelName($care->channel);
            }
        ],
        [
            'attribute' => 'type',
            'options' => ['width' => 110],
            'filter' => CareHelper::getTypeArray(),
            'format' => 'raw',
            'value' => function (Care $model) {
                return Html::tag('span', CareHelper::getTypeName($model->type), [
                    'class' => 'care-table__text--' . CareHelper::getTypeKey($model->type)
                ]);
            }
        ],
        [
            'options' => ['width' => 86],
            'format' => 'raw',
            'value' => function (Care $model) {
                $result = '';
                $steps = CareHistoryHelper::getSteps($model);
                foreach ($steps as $step) {
                    if ($step){
                        $result .= Html::tag('span', '', [
                            'title' => $step['diff'],
                            'class' => 'care-step__item care-step__item--' . $step['status']
                        ]);
                    } else {
                        $result .= Html::tag('span', '', ['class' => 'care-step__item']);
                    }
                }

                return Html::tag('span', $result, [
                    'class' => 'care-step'
                ]);
            }
        ],
    ],
]) ?>