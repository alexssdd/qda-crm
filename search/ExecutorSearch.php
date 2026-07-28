<?php

namespace app\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\core\helpers\PhoneHelper;
use app\modules\order\models\Executor;
use app\core\behaviors\DateRangeBehavior;

class ExecutorSearch extends Model
{
    public $name;
    public $phone;
    public $country_code;
    public $is_verified;
    public $status;
    public $location_name;
    public $service_type;
    public $registered_at;
    public $registered_from;
    public $registered_to;

    public function formName(): string
    {
        return 's';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => DateRangeBehavior::class,
                'attribute' => 'registered_at',
                'dateStartAttribute' => 'registered_from',
                'dateEndAttribute' => 'registered_to',
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['is_verified', 'status', 'service_type'], 'integer'],
            [['name', 'phone', 'location_name'], 'string', 'max' => 255],
            [['country_code'], 'string', 'max' => 5],
            [['registered_at'], 'string', 'max' => 100],
            [['registered_at'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Executor::find()
            ->alias('executor')
            ->with(['country', 'location', 'services']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['updated_at' => SORT_DESC],
                'attributes' => [
                    'name' => [
                        'asc' => ['executor.name' => SORT_ASC],
                        'desc' => ['executor.name' => SORT_DESC],
                    ],
                    'phone' => [
                        'asc' => ['executor.phone' => SORT_ASC],
                        'desc' => ['executor.phone' => SORT_DESC],
                    ],
                    'country_code' => [
                        'asc' => ['executor.country_code' => SORT_ASC],
                        'desc' => ['executor.country_code' => SORT_DESC],
                    ],
                    'rating' => [
                        'asc' => ['executor.rating' => SORT_ASC],
                        'desc' => ['executor.rating' => SORT_DESC],
                    ],
                    'is_verified' => [
                        'asc' => ['executor.is_verified' => SORT_ASC],
                        'desc' => ['executor.is_verified' => SORT_DESC],
                    ],
                    'orders_completed' => [
                        'asc' => ['executor.orders_completed' => SORT_ASC],
                        'desc' => ['executor.orders_completed' => SORT_DESC],
                    ],
                    'orders_canceled' => [
                        'asc' => ['executor.orders_canceled' => SORT_ASC],
                        'desc' => ['executor.orders_canceled' => SORT_DESC],
                    ],
                    'status' => [
                        'asc' => ['executor.status' => SORT_ASC],
                        'desc' => ['executor.status' => SORT_DESC],
                    ],
                    'registered_at' => [
                        'asc' => ['executor.registered_at' => SORT_ASC],
                        'desc' => ['executor.registered_at' => SORT_DESC],
                    ],
                    'updated_at' => [
                        'asc' => ['executor.updated_at' => SORT_ASC],
                        'desc' => ['executor.updated_at' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // Fail-closed: битый фильтр не должен расширять выборку
            $query->andWhere('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'executor.country_code' => $this->country_code,
            'executor.is_verified' => $this->is_verified,
            'executor.status' => $this->status,
        ]);

        $query->andFilterWhere([
            'between',
            'executor.registered_at',
            $this->registered_from,
            $this->registered_to,
        ]);

        $query->andFilterWhere(['like', 'executor.name', $this->name])
            ->andFilterWhere(['like', 'executor.phone', PhoneHelper::getCleanNumber($this->phone)]);

        if ($this->location_name !== null && $this->location_name !== '') {
            $query->joinWith('location location')
                ->andWhere(['like', 'location.name', $this->location_name]);
        }

        if ($this->service_type !== null && $this->service_type !== '') {
            $query->joinWith('services executorService')
                ->andWhere(['executorService.type' => (int) $this->service_type])
                ->distinct();
        }

        return $dataProvider;
    }
}
