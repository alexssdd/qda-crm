<?php

namespace app\services;

use Exception;
use DomainException;
use app\entities\User;
use app\entities\Advert;
use app\core\ActiveRecord;
use app\entities\AdvertUser;
use app\forms\AdvertCreateForm;
use app\forms\AdvertUpdateForm;
use app\core\helpers\AdvertHelper;

/**
 * Advert service
 */
class AdvertService
{
    /**
     * @param AdvertCreateForm $form
     * @return Advert
     * @throws Exception
     */
    public function create(AdvertCreateForm $form): Advert
    {
        $model = new Advert();
        $model->name = $form->name;
        $model->text = $form->text;
        $model->status = $form->status;
        $model->begin_at = strtotime($form->begin_at);
        $model->end_at = strtotime($form->end_at);
        $model->created_at = time();
        $model->updated_at = time();

        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }

        return $model;
    }

    /**
     * @param Advert $model
     * @param AdvertUpdateForm $form
     * @return void
     * @throws Exception
     */
    public function update(Advert $model, AdvertUpdateForm $form)
    {
        $model->name = $form->name;
        $model->text = $form->text;
        $model->status = $form->status;
        $model->begin_at = strtotime($form->begin_at);
        $model->end_at = strtotime($form->end_at);
        $model->updated_at = time();

        if (!$model->save()) {
            throw new DomainException($model->getErrorSummary(true)[0]);
        }
    }

    /**
     * @param User $user
     * @return array|ActiveRecord|null|Advert
     */
    public function getActiveAdvert(User $user): ?Advert
    {
        return Advert::find()
            ->alias('advert')
            ->leftJoin(['advertUsers' => AdvertUser::tableName()], implode(' AND ', [
                'advert.id = advertUsers.advert_id',
                'advertUsers.user_id = ' . $user->id
            ]))
            ->andWhere(['advert.status' => AdvertHelper::STATUS_ACTIVE])
            ->andWhere(['between', 'advert.begin_at', 'advert.end_at', time()])
            ->andWhere('advertUsers.id IS NULL')
            ->orderBy(['advert.id' => SORT_ASC])
            ->limit(1)
            ->one();
    }

    /**
     * @param Advert $advert
     * @param User $user
     * @return void
     * @throws Exception
     */
    public function setShowed(Advert $advert, User $user)
    {
        $model = AdvertUser::findOne([
            'advert_id' => $advert->id,
            'user_id' => $user->id,
        ]);

        if ($model){
            return;
        }

        $model = new AdvertUser();
        $model->advert_id = $advert->id;
        $model->user_id = $user->id;

        if (!$model->save()){
            throw new DomainException($model->getErrorSummary(true)[0]);
        }
    }
}