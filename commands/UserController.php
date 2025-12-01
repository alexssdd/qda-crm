<?php

namespace app\commands;

use Exception;
use app\entities\User;
use app\entities\Token;
use yii\console\Controller;
use yii\helpers\BaseConsole;
use app\core\helpers\UserHelper;

/**
 * User controller
 */
class UserController extends Controller
{
    /**
     * @return void
     * @throws Exception
     */
    public function actionCreate()
    {
        $model = new User();
        $this->readValue($model, 'phone');
        $model->setPassword($this->prompt('Password:', [
            'required' => true,
            'pattern' => '#^.{8,255}$#i',
            'error' => 'More than 8 symbols',
        ]));
        $model->generateAuthKey();
        $model->created_at = time();
        $model->updated_at = time();

        $this->log($model->save());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionActivate()
    {
        $phone = $this->prompt('Phone:', ['required' => true]);
        $model = $this->findModel($phone);
        $model->status = UserHelper::STATUS_ACTIVE;
        $model->removeVerifiedToken();
        $this->log($model->save());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function actionToken()
    {
        $phone = $this->prompt('Phone:', ['required' => true]);
        $model = $this->findModel($phone);

        $token = new Token();
        $token->user_id = $model->id;
        $token->generateToken(time() + 3600 * 24 * 365 * 5);

        $this->log($token->save());
    }

    /**
     * @param $model
     * @param $attribute
     * @return void
     */
    private function readValue($model, $attribute)
    {
        $model->$attribute = $this->prompt(mb_convert_case($attribute, MB_CASE_TITLE, 'utf-8') . ':', [
            'validator' => function ($input, &$error) use ($model, $attribute) {
                $model->$attribute = $input;
                if ($model->validate([$attribute])) {
                    return true;
                } else {
                    $error = implode(',', $model->getErrors($attribute));
                    return false;
                }
            },
        ]);
    }

    /**
     * @param $phone
     * @return User
     * @throws Exception
     */
    private function findModel($phone): User
    {
        if (!$model = User::findOne(['phone' => $phone])) {
            throw new Exception('User is not found');
        }

        return $model;
    }

    /**
     * @param $success
     * @return void
     */
    private function log($success)
    {
        if ($success) {
            $this->stdout('Success!', BaseConsole::FG_GREEN, BaseConsole::BOLD);
        } else {
            $this->stderr('Error!', BaseConsole::FG_RED, BaseConsole::BOLD);
        }
        echo PHP_EOL;
    }
}