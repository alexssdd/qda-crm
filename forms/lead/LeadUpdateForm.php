<?php

namespace app\forms\lead;

use Exception;
use app\entities\Lead;
use app\core\forms\Form;
use app\core\helpers\LeadHelper;

/**
 * Lead update form
 */
class LeadUpdateForm extends Form
{
    public $city_id;
    public $status;

    /**
     * @param Lead $lead
     * @param array $config
     */
    public function __construct(Lead $lead, array $config = [])
    {
        $this->city_id = $lead->city_id;
        $this->status = $lead->status;

        parent::__construct($config);
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function rules(): array
    {
        return [
            [['city_id'], 'integer'],
            [['status'], 'in', 'range' => array_keys(LeadHelper::getAvailableStatusArray())]
        ];
    }
}