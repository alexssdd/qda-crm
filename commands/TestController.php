<?php

namespace app\commands;

use Yii;
use Exception;
use DomainException;
use app\entities\Store;
use app\entities\Order;
use app\entities\Product;
use yii\helpers\VarDumper;
use app\entities\Merchant;
use app\entities\Customer;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use app\entities\OrderProduct;
use app\core\helpers\UserHelper;
use app\core\helpers\TextHelper;
use app\core\helpers\CityHelper;
use app\modules\sms\SmsTemplate;
use app\services\OperatorService;
use app\core\helpers\OrderHelper;
use yii\web\NotFoundHttpException;
use app\modules\sms\providers\SmsC;
use yii\base\InvalidConfigException;
use app\core\helpers\MerchantHelper;
use app\core\helpers\CustomerHelper;
use app\modules\sms\services\SmsService;
use app\modules\edna\helpers\EdnaHelper;
use app\services\order\OrderBonusService;
use app\modules\edna\services\EdnaService;
use app\modules\mail\services\MailService;
use app\modules\devino\helpers\DevinoHelper;
use app\modules\devino\services\DevinoService;
use app\modules\infobip\helpers\InfobipHelper;
use app\modules\infobip\services\InfobipService;
use app\modules\telegram\services\TelegramService;
use app\modules\yandex\services\YandexTaxiService;
use app\modules\picker\jobs\PickerAssemblyNotifyJob;
use app\modules\stock\services\StockAvailableService;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Test controller
 */
class TestController extends Controller
{
    /**
     * @return void
     * @throws Exception
     */
    public function actionNotifyPartners()
    {
        $number = $this->prompt('Number:', ['required' => true]);

        if (!$order = Order::findOne(['number' => $number])) {
            throw new DomainException("Order not found");
        }

        $partners = Customer::find()
            ->andWhere([
                'city_id' => $order->city_id,
            ])
            ->all();

        $telegram = new TelegramService();

        foreach ($partners as $partner) {
            if (!$telegramId = CustomerHelper::getTelegramId($partner)) {
                continue;
            }

            $message = TextHelper::orderTelegramNewMessage($order);
            $telegram->send($telegramId, $message);
        }

    }

    /**
     * @return void
     */
    public function actionOperators()
    {
        $users = (new OperatorService())->getOperators();
        foreach ($users as $user) {
            $this->stdout(implode(' | ', [
                $user->id,
                $user->phone,
                $user->full_name,
                $user->orders_count
            ]) . PHP_EOL);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionPayLink()
    {
        $number = $this->prompt('Number:', ['required' => true, 'default' => '7568']);
        $phone = $this->prompt('Number:', ['required' => true, 'default' => '77027212121']);

        if (!$order = Order::findOne(['number' => $number])) {
            throw new DomainException("Order number: $number not found");
        }

        $link = OrderHelper::getPayLink($order->number);

        $sms = new SmsService((new SmsC()));
        $message = SmsTemplate::orderPay($link);
        $sms->send($phone, $message);

        VarDumper::dump($link); die;
    }

    /**
     * @return void
     * @throws InvalidConfigException
     */
    public function actionYandexTaxiPrice()
    {
        $store = Store::findOne(1);

        $service = new YandexTaxiService();
        $cost = $service->getPrice($store, 43.24675023575692, 76.93559447082518);

        \yii\helpers\VarDumper::dump($cost, 10, false);die;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionStock()
    {
        $merchant = Merchant::findOne(['code' => MerchantHelper::CODE_MARWIN]);
        $products = [
            [
                'sku' => '003102',
                'quantity' => 1
            ]
        ];

        $service = new StockAvailableService($merchant, CityHelper::ID_ALMATY, $products);
        $result = $service->getStores();
        \yii\helpers\VarDumper::dump($result, 10, false);die;
    }

    /**
     * @return void
     * @throws NotFoundHttpException
     */
    public function actionOrderNotify(): void
    {
        $id = $this->prompt('Order ID:', [
            'required' => true
        ]);

        $order = Order::findOne($id);
        if (!$order){
            throw new NotFoundHttpException('The order was not found: ' . $id);
        }

        Yii::$app->queue->push(new PickerAssemblyNotifyJob([
            'order_id' => $order->id,
            'store_id' => $order->store_id,
            'remove' => true
        ]));
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionCustomerDate(): void
    {
        /** @var Customer[] $customers */
        $customers = Customer::find()
            ->andWhere(['created_at' => null])
            ->all();

        foreach ($customers as $customer){
            $createdAt = Order::find()->andWhere(['customer_id' => $customer->id])->min('created_at');
            if (!$createdAt){
                continue;
            }

            $customer->created_at = $createdAt;
            $customer->updated_at = $createdAt;
            $customer->save();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionStoreData()
    {
        $data = [
            'АА_Р_01;ChIJadkDFStvgzgRJqZAZoK1Udg',
            'АА_Р_15;ChIJrce1zedogzgRy3COu4ZzmJg',
            'АА_Р_119;ChIJcctz3gVpgzgRapU05M5gMHU',
            'АА_Р_175;ChIJ9YoxqqhugzgRweSVjCWLGcg',
            'АА_Р_176;ChIJgw1VbgJvgzgRoRznPiX4EtU',
            'АА_Р_183;ChIJJQrD8jBvgzgRiFu9wj-UP5U',
            'АА_Р_188;ChIJSbWGMaxpgzgRIitBXVeIX2A',
            'АК_Р_01;ChIJf0vnVGUhgkERlagjAEUdMjc',
            'АС_Р_02;ChIJ_72R0dSARUIRhJavsEmg4DU',
            'АС_Р_153;ChIJORU_lB-ERUIRq2F7QNACCvI',
            'АС_Р_155;ChIJ23XvbJeGRUIRvABTiKBQjE4',
            'АС_Р_157;ChIJVVUF6a6FRUIR5KR2bRx1lFA',
            'АС_Р_19;ChIJYzk6UL6GRUIRWWzJsBoiBYk',
            'АС_Р_28;ChIJtfNBfJ6DRUIRHCDcYs3Fys4',
            'АС_Р_32;ChIJdSBAg6mHRUIRXsbzKt6EG7s',
            'АС_Р_71;ChIJE60u0oSGRUIR64ZZFg-i1OE',
            'КА_Р_04;ChIJ64MI5y9HQ0IRf0jJfmy53F8',
            'КО_Р_01;ChIJ_U3EU4eKzEMRriPbVxo5HAA',
            'ПА_Р_26;ChIJ4dw2bDE1-EIRoiE7sIuhCr8',
            'ПТ_Р_01;ChIJeayDFgY7skMRlgGePtuUyzI',
            'СМ_Р_01;ChIJazz2KzZl8kIRE-9DyEhW5hc',
            'ТА_Р_02;ChIJEWwLKtQCpzgRs2gANO1IsV0',
            'УК_Р_03;ChIJvbkDtVJP60IRnLCqeDDA2Rk',
            'УР_Р_05;ChIJXxx79ma6cUER70fs9YURA4s',
            'ШМ_Р_03;ChIJOQUb2R8cqTgR9aMNkCik_js',
            'АТ_Р_01;ChIJW1qK8Yjpo0ER4qj94yvS6nQ',
            'АУ_Р_08;ChIJW6iGGQwxtEER3FFCYHBdyOs',
        ];

        foreach ($data as $item){
            $parts = explode(';', $item);

            $store = Store::findOne(['number' => $parts[0]]);
            if (!$store){
                $this->stdout('The store was not found: ' . $parts[0] . PHP_EOL);
                continue;
            }

            $config = $store->config;
            $config['google_id'] = $parts[1];
            $store->config = $config;
            if (!$store->save()){
                $this->stdout('Store save error: ' . $parts[0] . PHP_EOL);
                continue;
            }

            $this->stdout('Store success: ' . $parts[0] . PHP_EOL);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionFixOrderProducts()
    {
        /** @var OrderProduct[] $orderProducts */
        $orderProducts = OrderProduct::find()
            ->andWhere(['product_id' => null])
            ->all();

        $products = [];
        foreach ($orderProducts as $orderProduct){
            $product = ArrayHelper::getValue($products, $orderProduct->sku);
            if (!$product){
                $product = Product::findOne(['sku' => $orderProduct->sku]);
            }
            if (!$product){
                continue;
            }

            // Set product
            $products[$orderProduct->sku] = $product;

            // Update order product
            $orderProduct->product_id = $product->id;
            $orderProduct->save();
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionClearCustomerVendor(): void
    {
        /** @var Customer[] $customers */
        $customers = Customer::find()->all();

        foreach ($customers as $customer){
            $config = $customer->config;
            ArrayHelper::remove($config, 'vendor_id');
            $customer->config = $config;
            $customer->save();
        }
    }

    /**
     * @return void
     * @throws TransportExceptionInterface
     */
    public function actionMail(): void
    {
        $service = new MailService();
        $service->sendMessage('onskyd@gmail.com', 'Test subject', 'Test text');
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionDevino(): void
    {
        $service = new DevinoService();
        $result = $service->sendMessage([
            'from' => DevinoHelper::SENDER_MELOMAN,
            'to' => '77027972250',
            'templateId' => 'welcome',
            'languageCode' => 'ru',
            'templateParams' => [
                '1' => 'Диас'
            ],
        ]);

        \yii\helpers\VarDumper::dump($result, 10, false);die;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionInfobip(): void
    {
        $service = new InfobipService();
        $result = $service->sendMessage([
            'sender' => InfobipHelper::SENDER_MELOMAN,
            'destinations' => [
                ['to' => '77027972250']
            ],
            'content' => ['text' => 'Test SMS message']
        ]);

        \yii\helpers\VarDumper::dump($result, 10, false);die;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionEdna(): void
    {
        $service = new EdnaService();
        $result = $service->sendMessage([
            'sender' => EdnaHelper::SENDER_MARWIN,
            'phone' => '77027972250',
            'templateId' => EdnaHelper::TEMPLATE_ORDER_CANCELLATION,
            'textVariables' => ['12314', 'Marwin Test'],
        ]);

        \yii\helpers\VarDumper::dump($result, 10, false);die;
    }

    /**
     * @param $id
     * @return void
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionOrderBonus($id): void
    {
        $order = Order::findOne($id);
        $user = UserHelper::getBot();

        if (!$order){
            throw new NotFoundHttpException('The order was not found');
        }

        // Distribute
        (new OrderBonusService($order, $user))->distribute();
    }
}