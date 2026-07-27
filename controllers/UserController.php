<?php

namespace app\controllers;

use Yii;
use Exception;
use DomainException;
use yii\web\Response;
use yii\web\Controller;
use app\search\UserSearch;
use app\forms\UserCreateForm;
use app\forms\UserUpdateForm;
use app\services\UserService;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use app\modules\auth\models\User;
use app\modules\auth\helpers\UserHelper;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @return array|array[]
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [UserHelper::ROLE_ADMINISTRATOR],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate()
    {
        $model = new UserCreateForm();

        if ($model->load(Yii::$app->request->post())) {
            try {
                if (!$model->validate()){
                    throw new DomainException($model->getErrorSummary(true)[0]);
                }

                // Run service
                (new UserService())->create($model);

                Yii::$app->session->setFlash('success', Yii::t('app', 'User successfully created'));
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(['index']);
        }

        return $this->renderAjax('_create', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $user = $this->findModel($id);
        $model = new UserUpdateForm($user);

        if ($model->load(Yii::$app->request->post())){
            try {
                if (!$model->validate()){
                    throw new DomainException($model->getErrorSummary(true)[0]);
                }

                $this->assertCanUpdate($user, $model);

                // Run service
                (new UserService())->update($user, $model);

                Yii::$app->session->setFlash('success', Yii::t('app', 'User successfully updated'));
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax('_update', [
            'model' => $model,
        ]);
    }

    /**
     * @param $id
     * @return User
     * @throws NotFoundHttpException
     */
    protected function findModel($id): User
    {
        $model = User::find()
            ->andWhere(['id' => $id])
            ->andWhere(['role' => array_keys(UserHelper::getRoleArray())])
            ->one();

        if ($model !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    private function assertCanUpdate(User $user, UserUpdateForm $form): void
    {
        if ((int) $user->id !== (int) Yii::$app->user->id) {
            return;
        }

        if ($form->role !== $user->role || $form->status !== (int) $user->status) {
            throw new DomainException('Нельзя изменить собственную роль или статус.');
        }
    }
}
