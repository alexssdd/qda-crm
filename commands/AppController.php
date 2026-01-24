<?php
namespace app\commands;

use app\entities\Country;
use app\modules\auth\helpers\UserHelper;
use yii\console\Controller;
use app\modules\auth\services\UserService;
use app\modules\location\services\ImportService;

class AppController extends Controller
{
    public function actionInit(): void
    {
        // Countries
        (new ImportService())->countries();
        
        /** @var Country $country */
        foreach (Country::find()->all() as $country) {
            try {
                (new ImportService())->locations($country->code);
            } catch (\Exception $e) {
                continue;
            }
        }
        
        // Users
        $users = [
            ['phone' => '70000000001', 'name' => 'Bot', 'role' => UserHelper::ROLE_BOT, 'country' => 'kz'],
            ['phone' => '77027212121', 'name' => 'Alex', 'role' => UserHelper::ROLE_ADMIN, 'country' => 'kz'],
            ['phone' => '77763899999', 'name' => 'Sake', 'role' => UserHelper::ROLE_ADMIN, 'country' => 'kz'],
        ];

        foreach ($users as $item) {
            $service = new UserService();
            $user = $service->create($item['phone'], $item['name'], $item['country'], $item['role']);
            $service->addOtpIdentity($user->id, $user->phone);
        }
    }
}