<?php

namespace app\search;

use yii\base\Model;
use app\entities\City;
use app\core\helpers\CityHelper;
use yii\data\ActiveDataProvider;

/**
 * City search
 */
class CitySearch extends Model
{
    public $country_id;
    public $name;
    public $name_kk;
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
            [['country_id', 'status'], 'integer'],
            [['name', 'name_kk'], 'string']
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
        $query = City::find()
            ->andWhere(['not', ['id' => CityHelper::ID_ALL]]);

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
            'country_id' => $this->country_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'name_kk', $this->name_kk]);

        return $dataProvider;
    }
}
