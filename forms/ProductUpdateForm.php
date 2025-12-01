<?php

namespace app\forms;

use Yii;
use Exception;
use yii\base\Model;
use app\entities\Product;
use yii\helpers\ArrayHelper;
use app\core\helpers\DataHelper;
use app\core\helpers\ProductHelper;

/**
 * Product update form
 */
class ProductUpdateForm extends Model
{
    public $merchant_id;
    public $brand_id;
    public $name;
    public $sku;
    public $status;
    
    // Export
    public $export_kaspi;
    public $export_ozon;
    public $export_wb;
    public $export_wolt;
    public $export_glovo;
    public $export_ye;
    public $export_halyk;
    public $export_jusan;
    public $export_forte;

    /**
     * @param Product $product
     * @param array $config
     * @throws Exception
     */
    public function __construct(Product $product, array $config = [])
    {
        $this->merchant_id = $product->merchant_id;
        $this->brand_id = $product->brand_id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->status = $product->status;

        // Export
        $exports = ProductHelper::getExports($product);
        $this->export_kaspi = ArrayHelper::getValue($exports, ProductHelper::CHANNEL_KASPI . '.status');
        $this->export_ozon = ArrayHelper::getValue($exports, ProductHelper::CHANNEL_OZON . '.status');
        $this->export_wb = ArrayHelper::getValue($exports, ProductHelper::CHANNEL_WB . '.status');
        $this->export_wolt = ArrayHelper::getValue($exports, ProductHelper::CHANNEL_WOLT . '.status');
        $this->export_glovo = ArrayHelper::getValue($exports, ProductHelper::CHANNEL_GLOVO . '.status');
        $this->export_ye = ArrayHelper::getValue($exports, ProductHelper::CHANNEL_YE . '.status');
        $this->export_halyk = ArrayHelper::getValue($exports, ProductHelper::CHANNEL_HALYK . '.status');
        $this->export_jusan = ArrayHelper::getValue($exports, ProductHelper::CHANNEL_JUSAN . '.status');
        $this->export_forte = ArrayHelper::getValue($exports, ProductHelper::CHANNEL_FORTE . '.status');

        parent::__construct($config);
    }

    /**
     * @return array|array[]
     */
    public function rules(): array
    {
        return [
            [['brand_id', 'status'], 'integer'],
            [['name'], 'string'],
            [['name', 'status'], 'required'],
            
            [[
                'export_kaspi', 'export_ozon', 'export_wb', 'export_wolt', 'export_glovo', 'export_ye',
                'export_halyk', 'export_jusan', 'export_forte'
            ], 'integer']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'merchant_id' => Yii::t('app', 'Merchant ID'),
            'brand_id' => Yii::t('app', 'Brand ID'),
            'name' => Yii::t('app', 'Name'),
            'sku' => Yii::t('app', 'Sku'),
            'status' => Yii::t('app', 'Status'),

            // Export
            'export_kaspi' => Yii::t('app', 'Kaspi Export'),
            'export_ozon' => Yii::t('app', 'Ozon Export'),
            'export_wb' => Yii::t('app', 'Wb Export'),
            'export_wolt' => Yii::t('app', 'Wolt Export'),
            'export_glovo' => Yii::t('app', 'Glovo Export'),
            'export_ye' => Yii::t('app', 'Ye Export'),
            'export_halyk' => Yii::t('app', 'Halyk Export'),
            'export_jusan' => Yii::t('app', 'Jusan Export'),
            'export_forte' => Yii::t('app', 'Forte Export'),
        ];
    }
}
