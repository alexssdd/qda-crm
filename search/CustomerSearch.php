<?php

namespace app\search;

use yii\base\Model;
use app\entities\Customer;
use yii\data\ActiveDataProvider;
use app\core\helpers\PhoneHelper;
use app\core\behaviors\DateRangeBehavior;

/**
 * Customer search
 */
class CustomerSearch extends Model
{
    public $name;
    public $phone;
    public $email;
    public $iin;
    public $type;
    public $ref;
    public $status;
    public $country_code;
    public $registered_at;
    public $registered_from;
    public $registered_to;

    /**
     * @return string
     */
    public function formName(): string
    {
        return 's';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['type', 'status'], 'integer'],
            [['name', 'phone', 'email', 'iin', 'ref'], 'string', 'max' => 255],
            [['country_code'], 'string', 'max' => 5],
            [['registered_at'], 'string', 'max' => 100],
            [['registered_at'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
        ];
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

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Customer::find()
            ->alias('customer')
            ->with('country');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['registered_at' => SORT_DESC],
                'attributes' => [
                    'name',
                    'phone',
                    'country_code',
                    'status',
                    'registered_at',
                    'orders_created',
                    'orders_completed',
                    'orders_canceled',
                    'last_order_at',
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // Fail-closed: битый фильтр не должен расширять выборку
            $query->andWhere('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'customer.iin' => $this->iin,
            'customer.type' => $this->type,
            'customer.ref' => $this->ref,
            'customer.status' => $this->status,
            'customer.country_code' => $this->country_code,
        ]);

        $query->andFilterWhere([
            'between',
            'customer.registered_at',
            $this->registered_from,
            $this->registered_to,
        ]);

        $query->andFilterWhere(['like', 'customer.name', $this->name])
            ->andFilterWhere(['like', 'customer.phone', $this->phone ? PhoneHelper::getCleanNumber($this->phone) : null])
            ->andFilterWhere(['like', 'customer.email', $this->email]);

        return $dataProvider;
    }
}
