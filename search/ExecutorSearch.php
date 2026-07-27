<?php

namespace app\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\core\helpers\PhoneHelper;
use app\modules\order\models\Executor;

class ExecutorSearch extends Model
{
    public $name;
    public $phone;
    public $country_code;
    public $is_verified;
    public $status;

    public function formName(): string
    {
        return 's';
    }

    public function rules(): array
    {
        return [
            [['is_verified', 'status'], 'integer'],
            [['name', 'phone'], 'string', 'max' => 255],
            [['country_code'], 'string', 'max' => 5],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Executor::find()->with('country');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['updated_at' => SORT_DESC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'country_code' => $this->country_code,
            'is_verified' => $this->is_verified,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone', PhoneHelper::getCleanNumber($this->phone)]);

        return $dataProvider;
    }
}
