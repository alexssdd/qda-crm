<?php

namespace app\search;

use yii\base\Model;
use app\entities\User;
use yii\data\ActiveDataProvider;
use app\core\helpers\UserHelper;
use app\core\helpers\PhoneHelper;

/**
 * User search
 */
class UserSearch extends Model
{
    public $full_name;
    public $role;
    public $phone;
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
            [['status'], 'integer'],
            [['full_name', 'role', 'phone'], 'string']
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
        $query = User::find()
            ->andWhere(['role' => array_keys(UserHelper::getRoleArray())]);

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
            'role' => $this->role,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'full_name', $this->full_name]);
        $query->andFilterWhere(['like', 'phone', PhoneHelper::getCleanNumber($this->phone)]);

        return $dataProvider;
    }
}
