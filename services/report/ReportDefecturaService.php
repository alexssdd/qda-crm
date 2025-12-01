<?php

namespace app\services\report;

use Yii;
use Exception;
use app\entities\City;
use app\entities\Report;
use app\entities\Merchant;
use app\entities\Defectura;
use yii\helpers\ArrayHelper;
use app\core\helpers\ReportHelper;
use yii\base\InvalidConfigException;
use Box\Spout\Common\Entity\Style\Style;
use app\forms\report\ReportDefecturaForm;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

/**
 * Class ReportDefecturaService
 * @package app\services\report
 */
class ReportDefecturaService
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
                'label' => 'Город',
                'value' => function ($model){
                    return $model['city'];
                }
            ],
            [
                'label' => 'Мерчант',
                'value' => function ($model){
                    return $model['merchant'];
                }
            ],
            [
                'label' => 'Код товара',
                'value' => function ($model){
                    return $model['product_sku'];
                }
            ],
            [
                'label' => 'Наименование товара',
                'value' => function ($model){
                    return $model['product_name'];
                }
            ],
            [
                'label' => 'Количество запроса',
                'value' => function ($model){
                    return $model['quantity'];
                }
            ],
            [
                'label' => 'Остаток',
                'value' => function ($model){
                    return $model['stock'];
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
        $form = new ReportDefecturaForm();
        $query = $form->getQuery($model->getParams());

        // Variables
        $merchants = Merchant::find()->indexBy('id')->all();
        $cities = City::find()->indexBy('id')->all();

        /** @var Defectura $defectura */
        foreach ($query->batch(500) as $defecturaArray) {
            foreach ($defecturaArray as $defectura) {
                /** @var City $city */
                $city = ArrayHelper::getValue($cities, $defectura->city_id);
                /** @var Merchant $merchant */
                $merchant = ArrayHelper::getValue($merchants, $defectura->merchant_id);

                $item = [
                    'created_date' => Yii::$app->formatter->asDatetime($defectura->created_at),
                    'city' => $city ? $city->name : '',
                    'merchant' => $merchant ? $merchant->name : '',
                    'product_sku' => $defectura['product_sku'],
                    'product_name' => $defectura['product_name'],
                    'quantity' => (float)$defectura['quantity'],
                    'stock' => (float)$defectura['stock'],
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