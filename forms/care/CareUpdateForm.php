<?php

namespace app\forms\care;

use Yii;
use Exception;
use app\entities\Care;
use app\core\forms\Form;
use app\core\helpers\CareHelper;

/**
 * Care update form
 */
class CareUpdateForm extends Form
{
    public $order_number;
    public $complaint_reason;
    public $complaint_validity;
    public $category;
    public $solution_measures;
    public $complaint_personal;
    public $compensation;
    public $store_number;
    public $final_status;
    public $delivery_late;
    public $status;

    private $_care;

    /**
     * @param Care $care
     * @param array $config
     */
    public function __construct(Care $care, array $config = [])
    {
        $this->_care = $care;
        $this->order_number = $care->order_number;
        $this->complaint_reason = $care->complaint_reason;
        $this->complaint_validity = $care->complaint_validity;
        $this->category = $care->category;
        $this->solution_measures = $care->solution_measures;
        $this->complaint_personal = $care->complaint_personal;
        $this->compensation = $care->compensation;
        $this->store_number = $care->store_number;
        $this->final_status = $care->final_status;
        $this->status = $care->status;

        parent::__construct($config);
    }

    /**
     * @return array[]
     * @throws Exception
     */
    public function rules(): array
    {
        return [
            [['order_number', 'final_status', 'delivery_late'], 'integer'],
            [['store_number', 'complaint_reason', 'complaint_validity', 'category', 'solution_measures', 'complaint_personal', 'compensation'], 'safe'],
            [['status'], 'in', 'range' => array_keys(CareHelper::getAvailableStatuses($this->_care))],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'order_number' => Yii::t('care', 'Order Number'),
            'complaint_reason' => Yii::t('care', 'Complaint Reason'),
            'complaint_validity' => Yii::t('care', 'Complaint Validity'),
            'complaint_personal' => Yii::t('care', 'Complaint Personal'),
            'category' => Yii::t('care', 'Category'),
            'solution_measures' => Yii::t('care', 'Solution Measures'),
            'compensation' => Yii::t('care', 'Compensation'),
            'final_status' => Yii::t('care', 'Final Status'),
            'status' => Yii::t('care', 'Status'),
        ];
    }
}