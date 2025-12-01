<?php

namespace app\services\report;

use Yii;
use Exception;
use app\entities\Lead;
use app\entities\User;
use app\entities\City;
use app\entities\Brand;
use app\entities\Report;
use yii\helpers\ArrayHelper;
use app\core\helpers\DateHelper;
use app\core\helpers\LeadHelper;
use app\core\helpers\ReportHelper;
use yii\base\InvalidConfigException;
use app\forms\report\ReportLeadForm;
use Box\Spout\Common\Entity\Style\Style;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

/**
 * Class ReportLeadService
 * @package app\services\report
 */
class ReportLeadService
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
     * @return void
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function generate(): void
    {
        $model = $this->_model;

        $writer = WriterEntityFactory::createXLSXWriter();
        $writer->openToFile(ReportHelper::getFilePath($model));

        // Default style
        $style = new Style();
        $style->setShouldWrapText(false);
        $writer->setDefaultRowStyle($style);

        $columns = [
            [
                'label' => 'Дата',
                'value' => function ($model){
                    return $model['created_date'];
                }
            ],
            [
                'label' => 'Номер',
                'value' => function ($model){
                    return $model['number'];
                }
            ],
            [
                'label' => 'Город',
                'value' => function ($model){
                    return $model['city'];
                }
            ],
            [
                'label' => 'Канал',
                'value' => function ($model){
                    return $model['channel'];
                }
            ],
            [
                'label' => 'Обработал',
                'value' => function ($model){
                    return $model['handler'];
                }
            ],
            [
                'label' => 'Время обработки',
                'value' => function ($model){
                    return $model['handle_time'];
                }
            ],
            [
                'label' => 'Бренд',
                'value' => function ($model){
                    return $model['brand'];
                }
            ],
            [
                'label' => 'Номер клиента',
                'value' => function ($model){
                    return $model['phone'];
                }
            ],
            [
                'label' => 'Имя клиента',
                'value' => function ($model){
                    return $model['name'];
                }
            ],
            [
                'label' => 'Тема обращения',
                'value' => function ($model){
                    return $model['title'];
                }
            ],
            [
                'label' => 'Статус',
                'value' => function ($model){
                    return $model['status'];
                }
            ]
        ];

        // Header
        $headerCells = [];
        foreach ($columns as $column) {
            $value = ArrayHelper::getValue($column, 'label');
            $headerCells[] = $value;
        }

        // Style for header
        $style = new Style();
        $style->setFontBold();

        // Add header
        $headerRow = WriterEntityFactory::createRowFromArray($headerCells, $style);
        $writer->addRow($headerRow);

        // Body
        $form = new ReportLeadForm();
        $query = $form->getQuery($model->getParams());

        // Variables
        $users = User::find()->indexBy('id')->all();
        $brands = Brand::find()->indexBy('id')->all();
        $cities = City::find()->indexBy('id')->all();

        /** @var Lead $lead */
        foreach ($query->batch(500) as $leadGroups) {
            foreach ($leadGroups as $lead) {
                /** @var City $city */
                $city = ArrayHelper::getValue($cities, $lead->city_id);
                /** @var Brand $brand */
                $brand = ArrayHelper::getValue($brands, $lead->brand_id);
                /** @var User $handler */
                $handler = ArrayHelper::getValue($users, $lead->handler_id);

                $item = [
                    'created_date' => Yii::$app->formatter->asDatetime($lead->created_at),
                    'number' => $lead->number,
                    'city' => $city?->name,
                    'channel' => LeadHelper::getChannelName($lead->channel),
                    'handler' => $handler ? $handler->full_name : '',
                    'handle_time' => $this->getHandleTime($lead),
                    'brand' => $brand ? $brand->name : '',
                    'phone' => $lead->phone,
                    'name' => $lead->name,
                    'title' => $lead->title,
                    'status' => LeadHelper::getStatusName($lead->status)
                ];

                $bodyCells = [];
                foreach ($columns as $column) {
                    $value = call_user_func($column['value'], $item);
                    $bodyCells[] = $value;
                }

                $row = WriterEntityFactory::createRowFromArray($bodyCells);
                $writer->addRow($row);
            }
        }

        $writer->close();
    }

    /**
     * @param Lead $lead
     * @return string
     */
    protected function getHandleTime(Lead $lead): string
    {
        $startAt = null;
        $endAt = null;

        foreach ($lead->histories as $history) {
            // Set start at
            if (!$startAt && $history->status_after == LeadHelper::STATUS_PROCESS){
                $startAt = $history->created_at;
            }

            // Set end at
            if (in_array($history->status_after, LeadHelper::getFinishedStatuses())){
                $endAt = $history->created_at;
            }
        }

        if (!$startAt || !$endAt){
            return '';
        }

        return DateHelper::getGmDate($endAt - $startAt);
    }
}