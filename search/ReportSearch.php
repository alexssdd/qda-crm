<?php

namespace app\search;

use yii\base\Model;
use app\entities\Report;
use yii\data\ActiveDataProvider;
use app\core\helpers\UserHelper;

/**
 * Class ReportSearch
 * @package backend\models\report
 */
class ReportSearch extends Model
{
    public $type;
    public $comment;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['type', 'comment', 'status'], 'safe'],
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
        $query = Report::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'type' => $this->type,
            'status' => $this->status,
            'user_id' => UserHelper::getIdentity()->id
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
