<?php

namespace app\services\report;

use Exception;
use DomainException;
use app\entities\Report;
use app\services\ConsoleService;
use app\core\helpers\ReportHelper;
use app\forms\report\ReportCreateForm;

/**
 * Class ReportService
 * @package app\services\report
 */
class ReportService
{
    private $_model;

    /**
     * VariantService constructor.
     * @param Report $model
     */
    public function __construct(Report $model)
    {
        $this->_model = $model;
    }

    /**
     * @param ReportCreateForm $form
     * @return Report
     * @throws Exception
     */
    public function create(ReportCreateForm $form): Report
    {
        $model = $this->_model;

        $model->user_id = $form->user_id;
        $model->type = $form->type;
        $model->date_from = $form->date_from;
        $model->date_to = $form->date_to;
        $model->comment = $form->comment;
        $model->params = $form->params;
        $model->status = ReportHelper::STATUS_PROCESS;
        $model->created_at = time();
        if (!$model->save()){
            throw new DomainException($model->getErrorSummary(true)[0]);
        }

        // Run report
        (new ConsoleService())->run('report/run', [$model->id]);

        return $model;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function generate(): void
    {
        $model = $this->_model;

        try {
            if ($model->type == ReportHelper::TYPE_ORDER){
                (new ReportOrderService($model))->generate();
            } elseif ($model->type == ReportHelper::TYPE_DEFECTURA){
                (new ReportDefecturaService($model))->generate();
            } elseif ($model->type == ReportHelper::TYPE_CARE){
                (new ReportCareService($model))->generate();
            } elseif ($model->type == ReportHelper::TYPE_LEAD){
                (new ReportLeadService($model))->generate();
            }

            $this->setDone();
        } catch (Exception $e){
            $this->setError();

            // Prepare params
            $params = $model->params;
            $params['error'] = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ];
            $model->params = $params;
            $model->save();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function setDone(): void
    {
        $model = $this->_model;
        $model->status = ReportHelper::STATUS_DONE;
        if (!$model->save()){
            throw new DomainException($model->getErrorSummary(true)[0]);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function setError(): void
    {
        $model = $this->_model;
        $model->status = ReportHelper::STATUS_ERROR;
        if (!$model->save()){
            throw new DomainException($model->getErrorSummary(true)[0]);
        }
    }
}