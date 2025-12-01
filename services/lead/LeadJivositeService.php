<?php

namespace app\services\lead;

use Exception;
use DomainException;
use app\entities\Lead;
use app\entities\User;
use app\entities\Config;
use app\entities\Customer;
use app\entities\Jivosite;
use yii\helpers\ArrayHelper;
use app\core\helpers\TextHelper;
use app\core\helpers\LeadHelper;
use app\core\helpers\UserHelper;
use app\core\helpers\PhoneHelper;
use app\core\helpers\ConfigHelper;
use app\forms\lead\LeadTransferForm;
use app\core\helpers\LeadEventHelper;

/**
 * Lead jivosite service
 */
class LeadJivositeService
{
    /**
     * @param Jivosite $jivosite
     * @param $data
     * @return void
     * @throws Exception
     */
    public function create(Jivosite $jivosite, $data)
    {
        $bot = UserHelper::getBot();
        $user = $this->getUser($data);
        $phone = $this->getPhone($data);
        $customer = Customer::findOne(['phone' => $phone]);
        $widget = $this->getWidget($data['widget_id']);

        $lead = new Lead();
        $lead->handler_id = $user ? $user->id : null;
        $lead->customer_id = $customer ? $customer->id : null;
        $lead->brand_id = ArrayHelper::getValue($widget, 'brand_id');
        $lead->channel = $this->getChannel(ArrayHelper::getValue($widget, 'type'));
        $lead->vendor_id = $jivosite->chat_id;
        $lead->vendor_number = $jivosite->chat_id;
        $lead->name = $this->getName($data, $customer);
        $lead->phone = $phone;
        $lead->created_at = time() - rand(1000, 86400);
        $lead->status = $user ? LeadHelper::STATUS_PROCESS : LeadHelper::STATUS_NEW;

        // Create lead
        if (!$lead->save()){
            throw new DomainException($lead->getErrorMessage());
        }

        // Save lead for number
        if (!$lead->save()){
            throw new DomainException($lead->getErrorMessage());
        }

        // Create history new
        (new LeadHistoryService($lead, $bot))->create(LeadHelper::STATUS_CREATED, LeadHelper::STATUS_NEW);

        // Create event
        (new LeadEventService($lead, $user))->create('', LeadEventHelper::TYPE_JIVOSITE_CREATED);

        // Create history process
        if ($user){
            (new LeadHistoryService($lead, $user))->create(LeadHelper::STATUS_NEW, LeadHelper::STATUS_PROCESS);
        }
    }

    /**
     * @param Jivosite $jivosite
     * @param $data
     * @return void
     * @throws Exception
     */
    public function finished(Jivosite $jivosite, $data)
    {
        /** @var Lead $lead */
        $lead = Lead::find()
            ->andWhere(['channel' => LeadHelper::getJivositeChannels()])
            ->andWhere(['vendor_id' => $jivosite->chat_id])
            ->one();

        if (!$lead){
            return;
        }

        $handler = $lead->handler;
        $phone = $this->getPhone($data);
        $customer = Customer::findOne(['phone' => $phone]);
        $name = $this->getName($data, $customer);

        $lead->customer_id = $customer ? $customer->id : $lead->customer_id;
        $lead->name = $name ?: $lead->name;
        $lead->phone = $phone ?: $lead->phone;

        // Update lead
        if (!$lead->save()){
            throw new DomainException($lead->getErrorMessage());
        }

        // User
        $user = $this->getUser($data);
        if ($user && $user->id !== $lead->handler_id){
            // Transfer lead
            $transferForm = new LeadTransferForm();
            $transferForm->executor_id = $user->id;
            (new LeadManageService($lead))->transfer($transferForm);

            // Assign lead
            (new LeadAssignService($lead, $user))->assign();

            // Create message
            $message = TextHelper::transferLead($user->full_name);
            (new LeadEventService($lead, $handler))->create($message, LeadEventHelper::TYPE_TRANSFER);
        }

        // Create event
        (new LeadEventService($lead, $user))->create('', LeadEventHelper::TYPE_JIVOSITE_FINISHED);
    }

    /**
     * @param Jivosite $jivosite
     * @param $data
     * @return void
     * @throws Exception
     */
    public function clientUpdated(Jivosite $jivosite, $data)
    {
        /** @var Lead $lead */
        $lead = Lead::find()
            ->andWhere(['channel' => LeadHelper::getJivositeChannels()])
            ->andWhere(['vendor_id' => $jivosite->chat_id])
            ->one();

        if (!$lead){
            return;
        }

        $phone = $this->getPhone($data);
        $customer = Customer::findOne(['phone' => $phone]);
        $name = $this->getName($data, $customer);

        $lead->customer_id = $customer ? $customer->id : $lead->customer_id;
        $lead->name = $name ?: $lead->name;
        $lead->phone = $phone ?: $lead->phone;

        // Update lead
        if (!$lead->save()){
            throw new DomainException($lead->getErrorMessage());
        }

        // Create event
        (new LeadEventService($lead, UserHelper::getBot()))->create('', LeadEventHelper::TYPE_JIVOSITE_CLIENT_UPDATED);
    }

    /**
     * @param $data
     * @return User|mixed|null
     * @throws Exception
     */
    protected function getUser($data): ?User
    {
        $email = $this->getAgentEmail($data);

        if (!$email){
            return UserHelper::getBot();
        }

        $users = User::find()->indexBy('email')->all();

        return ArrayHelper::getValue($users, $email, UserHelper::getBot());
    }

    /**
     * @param $data
     * @return mixed
     * @throws Exception
     */
    protected function getAgentEmail($data)
    {
        // Agent
        $agent = ArrayHelper::getValue($data, 'agent', []);

        if (!$agent){
            // Agents
            $agents = ArrayHelper::getValue($data, 'agents', []);
            $agent = ArrayHelper::getValue($agents, 0, []);
        }

        return ArrayHelper::getValue($agent, 'email');
    }

    /**
     * @param $data
     * @return string
     * @throws Exception
     */
    protected function getPhone($data): string
    {
        return PhoneHelper::getCleanNumber(ArrayHelper::getValue($data, 'visitor.phone'));
    }

    /**
     * @param $id
     * @return array|null
     * @throws Exception
     */
    protected function getWidget($id): ?array
    {
        $config = Config::findOne(['key' => ConfigHelper::KEY_JIVOSITE]);
        if (!$config){
            return [];
        }

        $widgets = ArrayHelper::getValue($config->values, 'widgets', []);

        foreach ($widgets as $widget) {
            if ($widget['id'] == $id){
                return [
                    'brand_id' => ArrayHelper::getValue($widget, 'brand_id'),
                    'type' => $widget['type']
                ];
            }
        }

        return [];
    }

    /**
     * @param $type
     * @return int
     */
    protected function getChannel($type): int
    {
        switch ($type){
            case 'ig':
                return LeadHelper::CHANNEL_JIVOSITE_INSTAGRAM;
            case 'wa':
                return LeadHelper::CHANNEL_JIVOSITE_WHATSAPP;
            default:
                return LeadHelper::CHANNEL_JIVOSITE_SITE;
        }
    }

    /**
     * @param $data
     * @param Customer|null $customer
     * @return string
     * @throws Exception
     */
    protected function getName($data, Customer $customer = null): string
    {
        if ($customer){
            return $customer->name;
        }

        $name = ArrayHelper::getValue($data, 'visitor.name');
        if ($name){
            return $name;
        }

        $number = ArrayHelper::getValue($data, 'visitor.number');
        if ($number){
            return 'Лид №' . $number;
        }

        return 'Без имени';
    }
}