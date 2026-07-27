<?php

namespace app\search;

use yii\base\Model;
use app\entities\Log;
use yii\data\ActiveDataProvider;

class LogSearch extends Model
{
    public $target;
    public $data;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['data'], 'string', 'max' => 1000],
            [['target'], 'string', 'max' => 100],
            [['status'], 'integer'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        $query = Log::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // Fail-closed: битый фильтр не должен расширять выборку
            $query->andWhere('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'target' => $this->target,
            'status' => $this->status,
        ]);

        if ($this->target && $this->data){
            $query->andWhere(['like', 'data', $this->data]);
        }

        return $dataProvider;
    }
}
