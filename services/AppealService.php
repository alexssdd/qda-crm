<?php

namespace app\services;

use Yii;
use Exception;
use DomainException;
use app\entities\Care;
use app\entities\Customer;
use app\core\helpers\UserHelper;
use app\core\helpers\CareHelper;
use app\core\helpers\PhoneHelper;
use app\core\helpers\CareEventHelper;
use app\forms\appeal\AppealCreateForm;
use app\services\care\CareEventService;
use app\forms\appeal\AppealCustomerForm;
use app\services\care\CareHistoryService;

/**
 * Care service
 */
class AppealService
{
    /**
     * @param AppealCreateForm  $form
     * @return Care
     * @throws Exception
     */
    public function createCare(AppealCreateForm $form): Care
    {
        // Create care
        $care = new Care();
        $care->language = $form->language;
        $care->created_by = $form->created_by;
        $care->handler_id = $form->created_by;
        $care->city_id = $form->city_id;
        $care->type = $form->type;
        $care->channel = $form->channel;
        $care->order_number = $form->order_number;

        // Customer
        $care->customer_id = $form->customer_id;
        $care->name = $form->name;
        $care->phone = $form->phone;

        // Info
        $care->text = $form->text;
        $care->count_request = $form->count_request;
        $care->count_problem = $form->count_problem;
        $care->delivery_late = $form->delivery_late;
        $care->complaint_object = $form->complaint_object;
        $care->rating = $form->rating;

        // Services fields
        $care->created_at = time();
        $care->status = CareHelper::STATUS_NEW;

        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Save care
            if (!$care->save(false)) {
                throw new DomainException('Care create error');
            }

            // Save care for number
            $care->save(false);

            // Create history
            $user = $care->createdBy ?? UserHelper::getBot();
            (new CareHistoryService($care, $user))->create(CareHelper::STATUS_CREATED, CareHelper::STATUS_NEW);

            // Create event
            if ($care->text){
                (new CareEventService($care, $user))->create($care->text, CareEventHelper::TYPE_CARE_TEXT);
            }

            $transaction->commit();

            return $care;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @param AppealCustomerForm $form
     * @return array|null
     */
    public function customer(AppealCustomerForm $form): ?array
    {
        $customer = Customer::findOne([
            'phone' => PhoneHelper::getCleanNumber($form->phone)
        ]);

        if (!$customer){
            return null;
        }

        return [
            'id' => $customer->id,
            'name' => $customer->name
        ];
    }
}