<?php

namespace app\search;

use yii\base\Model;
use app\entities\Address;
use yii\data\ActiveDataProvider;

/**
 * Address search
 */
class AddressSearch extends Model
{
    public $customer_id;
    public $city_id;
    public $address;
    public $status;

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
            [['customer_id', 'city_id', 'status'], 'integer'],
            [['address'], 'string']
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
        $query = Address::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'customer_id' => $this->customer_id,
            'city_id' => $this->city_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'address', $this->address]);

        return $dataProvider;
    }
}
