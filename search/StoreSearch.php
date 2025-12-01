<?php

namespace app\search;

use yii\base\Model;
use app\entities\Store;
use yii\data\ActiveDataProvider;

/**
 * Store search
 */
class StoreSearch extends Model
{
    public $id;
    public $merchant_id;
    public $city_id;
    public $name;
    public $number;
    public $address;
    public $type;
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
            [['id', 'merchant_id', 'city_id', 'type', 'status'], 'integer'],
            [['name', 'number', 'address'], 'string']
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Store::find()
            ->with(['merchant', 'city']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'merchant_id' => $this->merchant_id,
            'city_id' => $this->city_id,
            'number' => $this->number,
            'type' => $this->type,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'address', $this->address]);

        return $dataProvider;
    }
}
