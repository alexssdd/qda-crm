<?php

namespace app\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\core\helpers\PhoneHelper;
use app\modules\auth\models\User;
use app\modules\auth\helpers\UserHelper;

/**
 * User search
 */
class UserSearch extends Model
{
    public $name;
    public $role;
    public $phone;
    public $country;
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
            [['name', 'role', 'phone', 'country'], 'string', 'max' => 255],
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
            // Fail-closed: битый фильтр не должен расширять выборку
            $query->andWhere('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'role' => $this->role,
            'country' => $this->country,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'phone', PhoneHelper::getCleanNumber($this->phone)]);

        return $dataProvider;
    }
}
