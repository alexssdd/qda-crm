<?php

namespace app\search;

use yii\base\Model;
use app\entities\Lead;
use app\core\helpers\LeadHelper;
use app\core\helpers\PhoneHelper;
use app\core\behaviors\DateRangeBehavior;

/**
 * Lead search
 */
class LeadSearch extends Model
{
    public $number;
    public $name;
    public $phone;
    public $city_id;
    public $brand_id;
    public $channel;
    public $handler_id;

    public $date_range;
    public $date_from;
    public $date_to;

    /**
     * @return string
     */
    public function formName(): string
    {
        return '';
    }

    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => DateRangeBehavior::class,
                'dateStartAttribute' => 'date_from',
                'dateEndAttribute' => 'date_to',
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['number', 'city_id', 'brand_id', 'channel', 'handler_id'], 'integer'],
            [['name', 'phone'], 'safe'],
            [['date_range'], 'match', 'pattern' => '/^.+\s\-\s.+$/']
        ];
    }

    /**
     * @param array $params
     * @return array
     */
    public function search(array $params): array
    {
        $result = [
            LeadHelper::STATUS_NEW => [],
            LeadHelper::STATUS_PROCESS => [],
            LeadHelper::STATUS_PENDING => [],
            LeadHelper::STATUS_SUCCESS_B2B => [],
            LeadHelper::STATUS_SUCCESS_B2C => [],
            LeadHelper::STATUS_CLOSED => [],
            LeadHelper::STATUS_CANCELLED => [],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $result;
        }

        $query = Lead::find()
            ->orderBy(['id' => SORT_DESC]);

        // Filter by equal
        $query->andFilterWhere([
            'city_id' => $this->city_id,
            'brand_id' => $this->brand_id,
            'channel' => $this->channel,
            'handler_id' => $this->handler_id,
        ]);

        // Filter by date
        $query->andFilterWhere(['between', 'created_at', $this->date_from, $this->date_to]);

        // Filter by text
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone', PhoneHelper::getCleanNumber($this->phone)]);

        /** @var Lead[] $leads */
        $leads = $query->all();

        foreach ($leads as $lead){
            if (!array_key_exists($lead->status, $result)){
                continue;
            }

            $result[$lead->status][] = [
                'id' => $lead->id,
                'number' => $lead->number,
                'title' => LeadHelper::getTitle($lead),
                'channel' => $lead->channel,
                'created_at' => $lead->created_at
            ];
        }

        return $result;
    }
}
