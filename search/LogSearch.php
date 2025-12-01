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
            [['data', 'target'], 'string'],
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
