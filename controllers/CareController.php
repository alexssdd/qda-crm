<?php

namespace app\controllers;

use Yii;
use Exception;
use DomainException;
use yii\web\Response;
use app\entities\Care;
use yii\web\Controller;
use app\search\CareSearch;
use yii\filters\AccessControl;
use app\core\helpers\UserHelper;
use app\core\helpers\TextHelper;
use app\services\OperatorService;
use yii\web\NotFoundHttpException;
use app\forms\care\CareUpdateForm;
use app\forms\care\CareTransferForm;
use app\forms\care\CareSolutionForm;
use app\core\helpers\CareEventHelper;
use app\forms\care\CareChatMessageForm;
use app\services\care\CareEventService;
use app\services\care\CareAssignService;
use app\services\care\CareManageService;
use app\services\care\CareHistoryService;

/**
 * Care controller
 */
class CareController extends Controller
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
                        'roles' => [UserHelper::ROLE_OPERATOR],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex(): string
    {
        $searchModel = new CareSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $care = null;
        if ($searchModel->id){
            $care = $this->getCare($searchModel->id);
        }

        return $this->render('index', [
            'care' => $care,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id): Response
    {
        $care = $this->getCare($id);
        $prevStatus = $care->status;
        $form = new CareUpdateForm($care);
        $form->load(Yii::$app->request->post(), '');

        try {
            if (!$form->validate()) {
                throw new DomainException($form->getErrorMessage());
            }

            // Update status
            (new CareManageService($care))->update($form);

            // Create history
            (new CareHistoryService($care, UserHelper::getIdentity()))->create($prevStatus, $care->status);
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(Yii::$app->request->referrer ?: 'care/index');
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionTransfer($id)
    {
        $care = $this->getCare($id);
        $user = UserHelper::getIdentity();
        $model = new CareTransferForm();

        if ($model->load(Yii::$app->request->post())) {
            try {
                if (!$model->validate()) {
                    throw new DomainException($model->getErrorMessage());
                }

                $executor = $model->getExecutor();
                if (!$executor){
                    throw new DomainException('The executor was not found');
                }

                // Assign care
                (new CareAssignService($care, $executor))->assign();

                // Create message
                $message = TextHelper::careTransfer($executor->full_name);
                (new CareEventService($care, $user))->create($message, CareEventHelper::TYPE_TRANSFER);

                // Flash
                Yii::$app->session->setFlash('success', 'Обращение успешно передано');
            } catch (Exception $e) {
                // Flash
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(Yii::$app->request->referrer ?: 'care/index');
        }

        $users = (new OperatorService())->getAll();

        return $this->renderAjax('_transfer', [
            'model' => $model,
            'users' => $users
        ]);
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionSolution($id)
    {
        $care = $this->getCare($id);
        $user = UserHelper::getIdentity();
        $model = new CareSolutionForm();

        if ($model->load(Yii::$app->request->post())) {
            try {
                if (!$model->validate()) {
                    throw new DomainException($model->getErrorMessage());
                }

                // Transfer care
                (new CareManageService($care))->solution($model);

                // Create message
                (new CareEventService($care, $user))->create($model->text, CareEventHelper::TYPE_CARE_SOLUTION_TEXT);

                // Flash
                Yii::$app->session->setFlash('success', 'Решение успешно внесено');
            } catch (Exception $e) {
                // Flash
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(Yii::$app->request->referrer ?: 'care/index');
        }

        return $this->renderAjax('_solution', [
            'model' => $model
        ]);
    }

    /**
     * @param $id
     * @return Response
     */
    public function actionChatMessage($id): Response
    {
        try {
            $care = $this->getCare($id);
            $user = UserHelper::getIdentity();
            $model = new CareChatMessageForm();
            $model->load(Yii::$app->request->post());

            if (!$model->validate()) {
                throw new DomainException($model->getErrorMessage());
            }

            // Create message
            (new CareEventService($care, $user))->create($model->message, CareEventHelper::TYPE_MESSAGE);

            return $this->asJson([
                'status' => 'success',
                'data' => $this->renderPartial('detail/chat', [
                    'care' => $care
                ])
            ]);
        } catch (Exception $e) {
            return $this->asJson([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @param $id
     * @return Care|null
     * @throws NotFoundHttpException
     */
    protected function getCare($id): ?Care
    {
        if (($model = Care::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
