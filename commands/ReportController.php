<?php

namespace app\commands;

use Yii;
use app\entities\Report;
use yii\console\Controller;
use yii\web\NotFoundHttpException;
use app\services\report\ReportService;

/**
 * Class ReportController
 * @package console\controllers
 */
class ReportController extends Controller
{
    /**
     * @param $id
     * @throws NotFoundHttpException
     */
    public function actionRun($id)
    {
        $model = $this->findModel($id);

        (new ReportService($model))->generate();;
    }

    /**
     * @param $id
     * @return Report|null
     * @throws NotFoundHttpException
     */
    public function findModel($id): ?Report
    {
        if ($model = Report::findOne($id)) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}