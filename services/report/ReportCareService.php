<?php

namespace app\services\report;

use Yii;
use Exception;
use app\entities\Care;
use app\entities\User;
use app\entities\City;
use app\entities\Report;
use yii\helpers\ArrayHelper;
use app\core\helpers\CareHelper;
use app\core\helpers\ReportHelper;
use yii\base\InvalidConfigException;
use app\forms\report\ReportCareForm;
use Box\Spout\Common\Entity\Style\Style;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

/**
 * Class ReportCareService
 * @package app\services\report
 */
class ReportCareService
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
                'label' => 'Тип',
                'value' => function ($model){
                    return $model['type'];
                }
            ],
            [
                'label' => 'Имя ассистента',
                'value' => function ($model){
                    return $model['handler'];
                }
            ],
            [
                'label' => 'Имя клиента',
                'value' => function ($model){
                    return $model['name'];
                }
            ],
            [
                'label' => 'Номер заказа в СРМ',
                'value' => function ($model){
                    return $model['order_number'];
                }
            ],
            [
                'label' => 'Номер телефона клиента',
                'value' => function ($model){
                    return $model['phone'];
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
                'label' => 'Причина претензии',
                'value' => function ($model){
                    return $model['complaint_reason'];
                }
            ],
            [
                'label' => 'Категория',
                'value' => function ($model){
                    return $model['category'];
                }
            ],
            [
                'label' => 'Статус претензии',
                'value' => function ($model){
                    return $model['status'];
                }
            ],
            [
                'label' => 'Обоснованность жалобы клиента',
                'value' => function ($model){
                    return $model['complaint_validity'];
                }
            ],
            [
                'label' => 'Принятые меры',
                'value' => function ($model){
                    return $model['solution_measures'];
                }
            ],
            [
                'label' => 'Компенсация',
                'value' => function ($model){
                    return $model['compensation'];
                }
            ],
            [
                'label' => 'ФИО сотрудника',
                'value' => function ($model){
                    return $model['complaint_personal'];
                }
            ],
            [
                'label' => 'Наименование точки',
                'value' => function ($model){
                    return $model['store_number'];
                }
            ],
            [
                'label' => 'Оценка',
                'value' => function ($model){
                    return $model['rating'];
                }
            ],
            [
                'label' => 'Оценка клиента',
                'value' => function ($model){
                    return $model['callback_rating'];
                }
            ],
            [
                'label' => Yii::t('care', 'Final Status'),
                'value' => function ($model){
                    return $model['final_status'];
                }
            ],
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
        $form = new ReportCareForm();
        $form->load($model->getParams());
        $query = $form->getQuery();

        // Variables
        $users = User::find()->indexBy('id')->all();
        $cities = City::find()->indexBy('id')->all();

        /** @var Care $care */
        foreach ($query->batch(500) as $data) {
            foreach ($data as $care) {
                /** @var User $user */
                $user = ArrayHelper::getValue($users, $care->handler_id);
                /** @var City $city */
                $city = ArrayHelper::getValue($cities, $care->city_id);

                $item = [
                    'created_date' => Yii::$app->formatter->asDatetime($care->created_at),
                    'number' => $care->number,
                    'type' => CareHelper::getTypeName($care->type),
                    'handler' => $user?->full_name,
                    'name' => $care->name,
                    'order_number' => $care->order_number,
                    'phone' => $care->phone,
                    'city' => $city?->name,
                    'channel' => CareHelper::getChannelName($care->channel),
                    'complaint_reason' => $care->complaint_reason,
                    'category' => CareHelper::getCategory($care->category),
                    'status' => CareHelper::getStatusName($care->status),
                    'complaint_validity' => CareHelper::getComplaintValidity($care->complaint_validity),
                    'solution_measures' => $care->solution_measures,
                    'complaint_personal' => $care->complaint_personal,
                    'compensation' => $care->compensation,
                    'store_number' => $care->store_number,
                    'rating' => (float)$care->rating,
                    'final_status' => CareHelper::getFinalStatusName($care->final_status),
                    'callback_rating' => $care->callback_rating ? (int)$care->callback_rating : ''
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
}