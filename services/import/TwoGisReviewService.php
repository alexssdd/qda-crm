<?php

namespace app\services\import;

use Yii;
use Exception;
use DomainException;
use app\entities\Care;
use app\entities\Store;
use yii\httpclient\Client;
use app\services\LogService;
use yii\helpers\ArrayHelper;
use app\core\helpers\LogHelper;
use app\core\helpers\CareHelper;
use app\core\helpers\UserHelper;
use app\core\helpers\StoreHelper;
use app\core\helpers\CareEventHelper;
use app\services\care\CareEventService;
use app\services\care\CareHistoryService;

/**
 * Two gis review service
 */
class TwoGisReviewService
{
    /**
     * @return void
     * @throws Exception
     */
    public function import()
    {
        $client = new Client([
            'baseUrl' => 'https://public-api.reviews.2gis.com/2.0'
        ]);
        $user = UserHelper::getBot();

        /** @var Store[] $stores */
        $stores = Store::find()->andWhere(['status' => StoreHelper::STATUS_ACTIVE])->all();

        foreach ($stores as $store) {
            // Check id
            if (!$id = StoreHelper::getTwoGisId($store)){
                continue;
            }

            $data = $this->getData($client, $id);
            $data = ArrayHelper::getValue($data, 'reviews', []);

            // Check data
            if (!$data){
                continue;
            }

            foreach ($data as $item) {
                // Check date
                $date = ArrayHelper::getValue($item, 'date_created');
                $createdTime = strtotime(substr($date, 0, 10) . ' ' . substr($date, 11, 8));
                if ($createdTime < strtotime(date('2025-06-01'))){
                    continue;
                }

                // Check exits
                $exists = Care::find()
                    ->andWhere(['channel' => CareHelper::CHANNEL_REVIEW_TWO_GIS])
                    ->andWhere(['vendor_id' => $item['id']])
                    ->exists();
                if ($exists){
                    break;
                }

                // Create care
                $rating = (float)$item['rating'];
                $care = new Care();
                $care->language = CareHelper::LANGUAGE_RU;
                $care->created_by = $user->id;
                $care->city_id = $store->city_id;
                $care->channel = CareHelper::CHANNEL_REVIEW_TWO_GIS;
                $care->vendor_id = $item['id'];
                $care->type = $rating > 3 ? CareHelper::TYPE_POSITIVE : CareHelper::TYPE_NEGATIVE;

                // Customer
                $care->name = ArrayHelper::getValue($item, 'user.name');
                $care->phone = '70000000001';

                // Info
                $care->text = $item['text'];
                $care->rating = $rating;
                $care->store_number = $store->number;

                // Services fields
                $care->created_at = time();
                $care->status = CareHelper::STATUS_NEW;
                $care->extra_fields = [
                    'two_gis' => $item
                ];

                $transaction = Yii::$app->db->beginTransaction();

                try {
                    // Save care
                    if (!$care->save(false)) {
                        throw new DomainException('Care create error');
                    }

                    // Save care for number
                    $care->save(false);

                    // Create history
                    (new CareHistoryService($care, $user))->create(CareHelper::STATUS_CREATED, CareHelper::STATUS_NEW);

                    // Create event store
                    $message = 'Точка: ' . $store->name;
                    (new CareEventService($care, $user))->create($message, CareEventHelper::TYPE_CARE_TEXT);

                    // Create event text
                    if ($care->text){
                        (new CareEventService($care, $user))->create($care->text, CareEventHelper::TYPE_CARE_TEXT);
                    }

                    $transaction->commit();
                } catch (Exception $e) {
                    $transaction->rollBack();

                    // Create log
                    LogService::error(LogHelper::TARGET_TWO_GIS_IMPORT_REVIEW, ['data' => $item, 'error' => $e->getMessage()]);

                    continue;
                }
            }

            // Reload db
            Yii::$app->db->close();
        }
    }

    /**
     * @param Client $client
     * @param $id
     * @return array
     * @throws Exception
     */
    protected function getData(Client $client, $id): array
    {
        // Variables
        $params = [
            'limit' => '12',
            'is_advertiser' => 'false',
            'fields' => 'meta.providers,meta.branch_rating,meta.branch_reviews_count,meta.total_count,reviews.hiding_reason,reviews.is_verified',
            'without_my_first_review' => 'false',
            'rated' => 'true',
            'sort_by' => 'date_edited',
            'key' => '6e7e1929-4ea9-4a5d-8c05-d601860389bd',
            'locale' => 'ru_KZ',
        ];

        $response = $client->get('/branches/' . $id . '/reviews', $params)->send();
        if (!$response->isOk){
            LogService::error(LogHelper::TARGET_TWO_GIS_IMPORT_REVIEW, ['id' => $id, 'error' => $response->content]);
            return [];
        }

        return $response->data;
    }
}