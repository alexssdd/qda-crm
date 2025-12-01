<?php

namespace app\search;

use yii\base\Model;
use app\entities\Customer;
use yii\data\ActiveDataProvider;
use app\core\helpers\PhoneHelper;

/**
 * Customer search
 */
class CustomerSearch extends Model
{
    public $name;
    public $phone;
    public $email;
    public $iin;
    public $type;
    public $ref;
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
            [['type', 'status'], 'integer'],
            [['name', 'phone', 'email', 'iin', 'ref'], 'string']
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
        $query = Customer::find()
            ->with(['addresses', 'contracts']);

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
            'iin' => $this->iin,
            'type' => $this->type,
            'ref' => $this->ref,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone', $this->phone ? PhoneHelper::getCleanNumber($this->phone) : null])
            ->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
