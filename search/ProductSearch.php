<?php

namespace app\search;

use yii\base\Model;
use app\entities\Product;
use yii\data\ActiveDataProvider;

/**
 * Product search
 */
class ProductSearch extends Model
{
    public $merchant_id;
    public $brand_id;
    public $name;
    public $sku;
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
            [['merchant_id', 'brand_id', 'status'], 'integer'],
            [['name', 'sku'], 'string']
        ];
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
        $query = Product::find()
            ->with(['merchant', 'brand']);

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
            'merchant_id' => $this->merchant_id,
            'brand_id' => $this->brand_id,
            'sku' => $this->sku,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
