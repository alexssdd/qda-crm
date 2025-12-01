<?php

namespace app\controllers;

use Yii;
use Exception;
use DomainException;
use yii\web\Response;
use app\entities\Lead;
use yii\web\Controller;
use app\search\LeadSearch;
use yii\filters\AccessControl;
use app\core\helpers\LeadHelper;
use app\core\helpers\TextHelper;
use app\core\helpers\UserHelper;
use app\services\OperatorService;
use yii\web\NotFoundHttpException;
use app\forms\lead\LeadUpdateForm;
use app\forms\lead\LeadTransferForm;
use app\core\helpers\LeadEventHelper;
use app\forms\lead\LeadChatMessageForm;
use app\services\lead\LeadEventService;
use app\services\lead\LeadManageService;
use app\services\lead\LeadAssignService;
use app\services\lead\LeadHistoryService;

/**
 * Lead controller
 */
class LeadController extends Controller
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
     */
    public function actionIndex(): string
    {
        $searchModel = new LeadSearch();
        $result = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'result' => $result,
            'searchModel' => $searchModel
        ]);
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDetail($id): string
    {
        $model = $this->getLead($id);

        return $this->renderAjax('_detail', [
            'lead' => $model
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id): Response
    {
        $lead = $this->getLead($id);
        $prevStatus = $lead->status;
        $form = new LeadUpdateForm($lead);
        $form->load(Yii::$app->request->post(), '');

        try {
            if (!$form->validate()) {
                throw new DomainException($form->getErrorMessage());
            }

            // Update status
            (new LeadManageService($lead))->update($form);

            // Create history
            (new LeadHistoryService($lead, UserHelper::getIdentity()))->create($prevStatus, $lead->status);
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }

        return $this->redirect(Yii::$app->request->referrer ?: 'lead/index');
    }

    /**
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionTransfer($id)
    {
        $lead = $this->getLead($id);
        $user = UserHelper::getIdentity();
        $model = new LeadTransferForm();

        if ($model->load(Yii::$app->request->post())) {
            try {
                if (!$model->validate()) {
                    throw new DomainException($model->getErrorMessage());
                }

                $executor = $model->getExecutor();
                if (!$executor){
                    throw new DomainException('The executor was not found');
                }

                // Transfer lead
                (new LeadManageService($lead))->transfer($model);

                // Assign lead
                (new LeadAssignService($lead, $executor))->assign();

                // Create message
                $message = TextHelper::transferLead($executor->full_name);
                (new LeadEventService($lead, $user))->create($message, LeadEventHelper::TYPE_TRANSFER);

                // Flash
                Yii::$app->session->setFlash('success', 'Лид успешно передан');
            } catch (Exception $e) {
                // Flash
                Yii::$app->session->setFlash('error', $e->getMessage());
            }

            return $this->redirect(Yii::$app->request->referrer ?: 'lead/index');
        }

        $users = (new OperatorService())->getAll();

        return $this->renderAjax('_transfer', [
            'model' => $model,
            'users' => $users
        ]);
    }

    /**
     * @param $id
     * @return Response
     */
    public function actionChatMessage($id): Response
    {
        try {
            $lead = $this->getLead($id);
            $user = UserHelper::getIdentity();
            $model = new LeadChatMessageForm();
            $model->load(Yii::$app->request->post());

            if (!$model->validate()) {
                throw new DomainException($model->getErrorMessage());
            }

            // Create message
            (new LeadEventService($lead, $user))->create($model->message, LeadEventHelper::TYPE_MESSAGE);

            return $this->asJson([
                'status' => 'success',
                'data' => $this->renderPartial('detail/chat', [
                    'lead' => $lead
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
     * @return string
     */
    public function actionCreate(): string
    {
        return $this->renderAjax('_create');
    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     * @throws Exception
     */
    public function actionJivositeMessages($id): string
    {
        $lead = $this->getLead($id);

        return $this->renderPartial('_jivosite_messages', [
            'lead' => $lead,
            'chat' => LeadHelper::getJivositeChat($lead)
        ]);
    }

    /**
     * @param $id
     * @return Lead
     * @throws NotFoundHttpException
     */
    protected function getLead($id): Lead
    {
        if (($model = Lead::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}