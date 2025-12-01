<?php

namespace app\search\chart;

use Exception;
use yii\base\Model;
use app\entities\Jivosite;
use app\core\helpers\JivositeHelper;

/**
 * Class ChatSearch
 * @package app\search\chart
 */
class ChatSearch extends Model
{
    /** Attributes */
    public $date_from;
    public $date_to;

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [['date_from', 'date_to'], 'safe'],
            [['date_from', 'date_to'], 'validatePeriod']
        ];
    }

    /**
     * @param $attribute
     * @param $params
     * @return void
     */
    public function validatePeriod($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!$this->date_from && !$this->date_to){
                $this->addError($attribute, 'Empty period');
            }
        }
    }

    /**
     * @return string
     */
    public function formName(): string
    {
        return '';
    }

    /**
     * @param $params
     * @return array
     * @throws Exception
     */
    public function count($params): array
    {
        $this->load($params);

        if (!$this->validate()) {
            return [];
        }

        $query = Jivosite::find()
            ->select([
                'status',
                'COUNT(id) AS count'
            ])
            ->andWhere(['BETWEEN', 'created_at', $this->getDateFrom(), $this->getDateTo()])
            ->groupBy(['status'])
            ->asArray();

        $data = $query->all();
        $result = [
            'active' => 0,
            'finished' => 0
        ];

        foreach ($data as $item) {
            if ($item['status'] == JivositeHelper::STATUS_ACTIVE){
                $result['active'] += (int)$item['count'];
            }
            if ($item['status'] == JivositeHelper::STATUS_FINISHED){
                $result['finished'] += (int)$item['count'];
            }
        }

        return $result;
    }

    /**
     * @return false|int
     */
    protected function getDateFrom()
    {
        return strtotime($this->date_from . ' 00:00:00');
    }

    /**
     * @return false|int
     */
    protected function getDateTo()
    {
        return strtotime($this->date_to . ' 23:59:59');
    }
}