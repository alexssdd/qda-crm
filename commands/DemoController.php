<?php

namespace app\commands;

use Yii;
use Exception;
use DomainException;
use yii\helpers\Json;
use app\entities\City;
use app\entities\User;
use app\entities\Token;
use app\entities\Store;
use app\entities\Country;
use app\entities\Product;
use app\entities\Merchant;
use app\entities\PriceType;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use app\forms\UserCreateForm;
use app\services\CityService;
use app\services\UserService;
use app\forms\CityCreateForm;
use app\forms\StoreCreateForm;
use app\services\StoreService;
use app\entities\ProductExport;
use app\core\helpers\CityHelper;
use app\core\helpers\DataHelper;
use app\core\helpers\UserHelper;
use app\core\helpers\StoreHelper;
use app\services\MerchantService;
use app\core\helpers\CountryHelper;
use app\core\helpers\ProductHelper;
use app\core\helpers\MerchantHelper;

/**
 * Demo
 */
class DemoController extends Controller
{
    /**
     * @return void
     * @throws Exception
     */
    public function actionRun(): void
    {
        $this->actionUsers();
        $this->actionTokens();
        $this->actionMerchants();
        $this->actionCountries();
        $this->actionCities();
        $this->actionPriceTypes();
        $this->actionStores();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionUsers(): void
    {
        $data = [
            ['phone' => 70000000000, 'name' => 'Bot', 'role' => UserHelper::ROLE_BOT],

            ['phone' => 77027212121, 'name' => 'Алекс', 'role' => UserHelper::ROLE_ADMIN],
            ['phone' => 77027972250, 'name' => 'Диас', 'role' => UserHelper::ROLE_ADMIN],
            ['phone' => 77079252793, 'name' => 'Rose', 'role' => UserHelper::ROLE_ADMIN],
            ['phone' => 77775599010, 'name' => 'Anel', 'role' => UserHelper::ROLE_ADMIN],
            ['phone' => 70000000001, 'name' => 'Демо', 'role' => UserHelper::ROLE_ADMIN],
            ['phone' => 79267072600, 'name' => 'Pavel', 'role' => UserHelper::ROLE_ADMIN],

            ['phone' => 70000000002, 'name' => 'Service Vendor', 'role' => UserHelper::ROLE_SERVICE_VENDOR],
            ['phone' => 70000000003, 'name' => 'Service Picker', 'role' => UserHelper::ROLE_SERVICE_PICKER],
            ['phone' => 70000000004, 'name' => 'Service Pos', 'role' => UserHelper::ROLE_SERVICE_POS],
            ['phone' => 70000000005, 'name' => 'Service Delivery', 'role' => UserHelper::ROLE_SERVICE_DELIVERY],
            ['phone' => 70000000006, 'email' => 'kaspi@marwin.kz', 'name' => 'Service Kaspi', 'role' => UserHelper::ROLE_SERVICE_KASPI],
            ['phone' => 70000000007, 'name' => 'Service Glovo', 'role' => UserHelper::ROLE_SERVICE_GLOVO],
            ['phone' => 70000000008, 'name' => 'Service Yandex Eda', 'role' => UserHelper::ROLE_SERVICE_YANDEX_EDA],
            ['phone' => 70000000009, 'name' => 'Service Wms', 'role' => UserHelper::ROLE_SERVICE_WMS],
        ];

        foreach ($data as $item) {
            // Check exists
            if (User::findOne(['phone' => $item['phone']])){
                continue;
            }

            // Prepare form
            $form = new UserCreateForm();
            $form->full_name = $item['name'];
            $form->phone = $item['phone'];
            $form->email = ArrayHelper::getValue($item, 'email');
            $form->role = $item['role'];
            $form->status = UserHelper::STATUS_ACTIVE;
            $form->password = $item['phone'];
            $form->passwordRepeat = $item['phone'];

            // Set state
            if ($form->role == UserHelper::ROLE_OPERATOR){
                $form->state = UserHelper::STATE_ONLINE;
            }

            // Run service
            (new UserService())->create($form);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionTokens(): void
    {
        $data = [
            ['phone' => 70000000002, 'token' => '8Ab37dnrGlYdVw3oflDo3Hk4pbNivLEUeU3lWipx0XPMLMR2Phk3HusoYtuknpUKOACw2SiXJDqJGxYw2uVncSFdBNZ5kESECS1Pro1CofIGSAqWclFwP13B0SbHE63UzLFCC6nE8hmZxkEV5uzkVkrDYNAkuOWqU6tMtsiZXt5iQIirZh3BxwYhrr1d5IKpkDFV', 'expire' => time() + (86400 * 365 * 5)],
            ['phone' => 70000000003, 'token' => 'vRPVlVaGuwYX5yLOtwVHInOhKjYISH82XXVpHZAzuoobEKgcTTlQJVWgR9CIYesST7S6aZEHfQ3aNMOJfG6m9hqnHxcg7JS5eTiru8WZJryqpZyhOBjr9k2bWd8GgLHgYgoK4jUgszgQcWVfcpeG0t', 'expire' => time() + (86400 * 365 * 5)],
            ['phone' => 70000000004, 'token' => 'CSr6U3djkArV15OY23UBfz5LPIvqWjpu0iCYF1VvcalO2JmyPEXe5QaCaTuq2lgKc66mFulMxY7KEwFo4tEJdJCxvwioyylt0VZzapNN3RVtbi1VbD7GKX5V16iMFWRy', 'expire' => time() + (86400 * 365 * 5)]
        ];

        foreach ($data as $item) {
            // Check exists
            if (!$user = User::findOne(['phone' => $item['phone']])){
                continue;
            }
            // Check exists
            if (Token::find()->andWhere(['user_id' => $user->id, 'token' => $item['token']])->exists()){
                continue;
            }

            $model = new Token();
            $model->user_id = $user->id;
            $model->token = $item['token'];
            $model->expired_at = $item['expire'];
            $model->save(false);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionMerchants(): void
    {
        $data = [
            ['code' => MerchantHelper::CODE_MARWIN, 'name' => 'Marwin'],
        ];

        foreach ($data as $item) {
            // Check exists
            if (Merchant::findOne(['code' => $item['code']])){
                continue;
            }

            (new MerchantService(new Merchant()))->create($item['name'], $item['code']);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionCountries(): void
    {
        $data = [
            ['iso' => 'kz', 'name' => 'Казахстан', 'id' => 1],
        ];

        foreach ($data as $item) {
            // Check exists
            if (Country::findOne(['iso' => $item['iso']])){
                continue;
            }

            // Create model
            $model = new Country();
            $model->name = $item['name'];
            $model->iso = $item['iso'];
            $model->status = CountryHelper::STATUS_ACTIVE;
            if (!$model->save()){
                throw new DomainException($model->getErrorSummary(true)[0]);
            }
        }
    }

    /**
     * @return void
     */
    public function actionCities(): void
    {
        $data = [
            ['country_iso' => 'kz', 'name' => 'Все города', 'lat' => '0', 'lng' => '0', 'delivery_id' => 0],
            ['country_iso' => 'kz', 'name' => 'Алматы', 'lat' => '43.238293', 'lng' => '76.945465', 'delivery_id' => 1],
            ['country_iso' => 'kz', 'name' => 'Астана', 'lat' => '51.128207', 'lng' => '71.430411', 'delivery_id' => 2],
            ['country_iso' => 'kz', 'name' => 'Шымкент', 'lat' => '42.315514', 'lng' => '69.586907', 'delivery_id' => 5],
            ['country_iso' => 'kz', 'name' => 'Актобе', 'lat' => '50.283', 'lng' => '57.167', 'delivery_id' => 3],
            ['country_iso' => 'kz', 'name' => 'Актау', 'lat' => '43.650', 'lng' => '51.160', 'delivery_id' => 10],
            ['country_iso' => 'kz', 'name' => 'Атырау', 'lat' => '47.116', 'lng' => '51.883', 'delivery_id' => 14],
            ['country_iso' => 'kz', 'name' => 'Караганда', 'lat' => '49.802', 'lng' => '73.087', 'delivery_id' => 6],
            ['country_iso' => 'kz', 'name' => 'Костанай', 'lat' => '53.214', 'lng' => '63.624', 'delivery_id' => 76],
            ['country_iso' => 'kz', 'name' => 'Павлодар', 'lat' => '52.283', 'lng' => '76.967', 'delivery_id' => 77],
            ['country_iso' => 'kz', 'name' => 'Петропавловск', 'lat' => '54.873', 'lng' => '69.146', 'delivery_id' => 78],
            ['country_iso' => 'kz', 'name' => 'Семей', 'lat' => '50.411', 'lng' => '80.227', 'delivery_id' => 13],
            ['country_iso' => 'kz', 'name' => 'Тараз', 'lat' => '42.900', 'lng' => '71.367', 'delivery_id' => 11],
            ['country_iso' => 'kz', 'name' => 'Усть-Каменогорск', 'lat' => '49.957', 'lng' => '82.611', 'delivery_id' => 4],
            ['country_iso' => 'kz', 'name' => 'Уральск', 'lat' => '51.222', 'lng' => '51.377', 'delivery_id' => 8],
        ];

        foreach ($data as $item) {
            /** @var Country $country */
            $country = Country::findOne(['iso' => $item['country_iso']]);
            if (!$country){
                continue;
            }

            // Check exists
            if ($city = City::findOne(['name' => $item['name']])){
                $config = $city->config;
                $config['delivery_id'] = $item['delivery_id'];
                $config['lat'] = $item['lat'];
                $config['lng'] = $item['lng'];
                $city->config = $config;
                $city->save();
                continue;
            }

            // Prepare form
            $form = new CityCreateForm();
            $form->country_id = $country->id;
            $form->name = trim($item['name']);
            $form->status = CityHelper::STATUS_ACTIVE;
            $form->config = [
                'lat' => $item['lat'],
                'lng' => $item['lng'],
                'delivery_id' => $item['delivery_id']
            ];

            // Run service
            (new CityService())->create($form);
        }
    }

    /**
     * @return void
     * @throws \yii\db\Exception
     */
    public function actionPriceTypes(): void
    {
        $data = [
            ['id' => 1, 'code' => 'common', 'name' => 'Общая цена'],
        ];

        foreach ($data as $item) {
            // Check exists
            if (PriceType::findOne($item['id'])){
                continue;
            }

            $model = new PriceType();
            $model->id = $item['id'];
            $model->code = $item['code'];
            $model->name = $item['name'];
            $model->save(false);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionStores(): void
    {
        try {
            $data = Json::decode(file_get_contents(\Yii::getAlias('@storage/data/stores.json')));
        } catch (Exception $e){
            $data = [];
        }

        $deliveryNumber = 10000;
        foreach ($data as $item) {
            $deliveryNumber++;
            /** @var Store $store */
            if ($store = Store::find()->andWhere(['number' => $item['code_ax']])->one()){
                $config = $store->config;
                // $config['name_short'] = $item['name_short'] ?: ($item['name'] ?: $item['name_ax']);
                // $config['delivery_number'] = $deliveryNumber;
                $workingFrom = ArrayHelper::getValue($item, 'hours.mo.open_time');
                $workingTo = ArrayHelper::getValue($item, 'hours.mo.close_time');

                if (!$workingFrom || !$workingTo){
                    continue;
                }

                $workingTime = $workingFrom . ' - ' . $workingTo;
                if ($workingFrom == '00:00' && $workingTo == '24:00'){
                    $workingTime = 'Круглосуточно';
                }

                $config['working_time'] = $workingTime;
                $store->config = $config;
                $store->save();
                continue;
            }
            $city = City::findOne(['name' => $item['city']]);
            if (!$city){
                $this->stdout('City was not found: ' . $item['city']);
                continue;
            }

            // Prepare form
            $form = new StoreCreateForm();
            $form->merchant_id = 1;
            $form->city_id = $city->id;
            $form->type = StoreHelper::TYPE_SHOP;
            $form->name = $item['name'] ?: $item['name_ax'];
            $form->name_short = $item['name_short'] ?: $item['name'];
            $form->number = $item['code_ax'];
            $form->address = $item['address'];
            $form->lat = ArrayHelper::getValue($item, 'coordinates.lat');
            $form->lng = ArrayHelper::getValue($item, 'coordinates.lng');
            $form->phone = ArrayHelper::getValue($item, 'primary_phone.formatted');
            $form->delivery_number = $deliveryNumber;
            $form->status = StoreHelper::STATUS_ACTIVE;

            // Run service
            (new StoreService())->create($form);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionWoltProducts(): void
    {
        $fileName = Yii::getAlias('@storage/private/wolt_products.txt');

        // Prepare categories
        $data = file_get_contents($fileName);
        $data = explode("\n", $data);
        $data = array_map(function ($item){
            return trim($item);
        }, $data);

        foreach ($data as $sku){
            $product = Product::findOne(['sku' => $sku]);
            if (!$product){
                $this->stdout('The product was not found: ' . $sku . PHP_EOL);
                continue;
            }

            $productExport = ProductExport::findOne([
                'product_id' => $product->id,
                'channel' => ProductHelper::CHANNEL_WOLT
            ]);

            // Check status
            if ($productExport && $productExport->status == DataHelper::BOOL_YES){
                continue;
            }

            if (!$productExport){
                $productExport = new ProductExport();
                $productExport->product_id = $product->id;
                $productExport->sku = $product->sku;
                $productExport->channel = ProductHelper::CHANNEL_WOLT;
            }

            $productExport->status = DataHelper::BOOL_YES;
            $productExport->save();

            // Reload db
            Yii::$app->db->close();
        }
    }
}