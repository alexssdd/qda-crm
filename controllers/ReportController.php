<?php

namespace app\controllers;

use Yii;
use Exception;
use DomainException;
use yii\web\Response;
use yii\web\Controller;
use app\entities\Report;
use app\search\ReportSearch;
use yii\filters\AccessControl;
use app\core\helpers\UserHelper;
use app\core\helpers\ReportHelper;
use yii\web\NotFoundHttpException;
use app\forms\report\ReportLeadForm;
use app\forms\report\ReportCareForm;
use app\forms\report\ReportOrderForm;
use app\forms\report\ReportCreateForm;
use app\services\report\ReportService;
use app\forms\report\ReportDefecturaForm;

/**
 * Class ReportController
 * @package app\controllers
 */
class ReportController extends Controller
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
                        'roles' => [UserHelper::ROLE_ADMINISTRATOR]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function actionOrder(): string
    {
        $searchModel = new ReportSearch();
        $searchModel->type = ReportHelper::TYPE_ORDER;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('order', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionOrderModal()
    {
        $paramsForm = new ReportOrderForm();
        $createForm = new ReportCreateForm();
        $createForm->user_id = UserHelper::getIdentity()->id;
        $createForm->type = ReportHelper::TYPE_ORDER;

        if ($paramsForm->load(Yii::$app->request->post()) && $createForm->load(Yii::$app->request->post())) {
            try {
                if (!$paramsForm->validate()){
                    throw new DomainException($paramsForm->getErrorMessage());
                }
                if (!$createForm->validate()){
                    throw new DomainException($createForm->getErrorMessage());
                }
                if (!$paramsForm->downloadAvailable()){
                    throw new DomainException('Максимально можно формировать отчет только за 31 день');
                }

                $createForm->date_from = $paramsForm->date_from;
                $createForm->date_to = $paramsForm->date_to;
                $createForm->params = $paramsForm->getAttributes();

                (new ReportService(new Report()))->create($createForm);

                Yii::$app->session->addFlash('success', Yii::t('app', 'Report successfully created'));
            } catch (Exception $e){
                Yii::$app->session->addFlash('error', $e->getMessage());
            }

            return $this->redirect(['order']);
        }

        return $this->renderAjax('order_modal', [
            'paramsForm' => $paramsForm,
            'createForm' => $createForm,
        ]);
    }

    /**
     * @return string
     */
    public function actionDefectura(): string
    {
        $searchModel = new ReportSearch();
        $searchModel->type = ReportHelper::TYPE_DEFECTURA;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('defectura', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionDefecturaModal()
    {
        $paramsForm = new ReportDefecturaForm();
        $createForm = new ReportCreateForm();
        $createForm->user_id = UserHelper::getIdentity()->id;
        $createForm->type = ReportHelper::TYPE_DEFECTURA;

        if ($paramsForm->load(Yii::$app->request->post()) && $createForm->load(Yii::$app->request->post())) {
            try {
                if (!$paramsForm->validate()){
                    throw new DomainException($paramsForm->getErrorMessage());
                }
                if (!$createForm->validate()){
                    throw new DomainException($createForm->getErrorMessage());
                }
                if (!$paramsForm->downloadAvailable()){
                    throw new DomainException('Максимально можно формировать отчет только за 31 день');
                }

                $createForm->date_from = $paramsForm->date_from;
                $createForm->date_to = $paramsForm->date_to;
                $createForm->params = $paramsForm->getAttributes();

                (new ReportService(new Report()))->create($createForm);

                Yii::$app->session->addFlash('success', Yii::t('app', 'Report successfully created'));
            } catch (Exception $e){
                Yii::$app->session->addFlash('error', $e->getMessage());
            }

            return $this->redirect(['defectura']);
        }

        return $this->renderAjax('defectura_modal', [
            'paramsForm' => $paramsForm,
            'createForm' => $createForm,
        ]);
    }

    /**
     * @return string
     */
    public function actionCare(): string
    {
        $searchModel = new ReportSearch();
        $searchModel->type = ReportHelper::TYPE_CARE;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('care', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCareModal()
    {
        $paramsForm = new ReportCareForm();
        $createForm = new ReportCreateForm();
        $createForm->user_id = UserHelper::getIdentity()->id;
        $createForm->type = ReportHelper::TYPE_CARE;

        if ($paramsForm->load(Yii::$app->request->post()) && $createForm->load(Yii::$app->request->post())) {
            try {
                if (!$paramsForm->validate()){
                    throw new DomainException($paramsForm->getErrorMessage());
                }
                if (!$createForm->validate()){
                    throw new DomainException($createForm->getErrorMessage());
                }
                if (!$paramsForm->downloadAvailable()){
                    throw new DomainException('Максимально можно формировать отчет только за 31 день');
                }

                $createForm->date_from = $paramsForm->date_from;
                $createForm->date_to = $paramsForm->date_to;
                $createForm->params = $paramsForm->getAttributes();

                (new ReportService(new Report()))->create($createForm);

                Yii::$app->session->addFlash('success', Yii::t('app', 'Report successfully created'));
            } catch (Exception $e){
                Yii::$app->session->addFlash('error', $e->getMessage());
            }

            return $this->redirect(['care']);
        }

        return $this->renderAjax('care_modal', [
            'paramsForm' => $paramsForm,
            'createForm' => $createForm,
        ]);
    }

    /**
     * @return string
     */
    public function actionLead(): string
    {
        $searchModel = new ReportSearch();
        $searchModel->type = ReportHelper::TYPE_LEAD;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('lead', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionLeadModal()
    {
        $paramsForm = new ReportLeadForm();
        $createForm = new ReportCreateForm();
        $createForm->user_id = UserHelper::getIdentity()->id;
        $createForm->type = ReportHelper::TYPE_LEAD;

        if ($paramsForm->load(Yii::$app->request->post()) && $createForm->load(Yii::$app->request->post())) {
            try {
                if (!$paramsForm->validate()){
                    throw new DomainException($paramsForm->getErrorMessage());
                }
                if (!$createForm->validate()){
                    throw new DomainException($createForm->getErrorMessage());
                }
                if (!$paramsForm->downloadAvailable()){
                    throw new DomainException('Максимально можно формировать отчет только за 31 день');
                }

                $createForm->date_from = $paramsForm->date_from;
                $createForm->date_to = $paramsForm->date_to;
                $createForm->params = $paramsForm->getAttributes();

                (new ReportService(new Report()))->create($createForm);

                Yii::$app->session->addFlash('success', Yii::t('app', 'Report successfully created'));
            } catch (Exception $e){
                Yii::$app->session->addFlash('error', $e->getMessage());
            }

            return $this->redirect(['lead']);
        }

        return $this->renderAjax('lead_modal', [
            'paramsForm' => $paramsForm,
            'createForm' => $createForm,
        ]);
    }

    /**
     * @param $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDownload($id): Response
    {
        $model = $this->findModel($id);

        $folder = ReportHelper::getFolder() . $model->user_id;
        $file = $folder . '/' . $model->id . '.xlsx';

        return Yii::$app->response->sendFile($file);
    }

    /**
     * @param $id
     * @return Report
     * @throws NotFoundHttpException
     */
    public function findModel($id): Report
    {
        if ($model = Report::findOne($id)) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}