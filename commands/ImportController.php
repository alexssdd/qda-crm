<?php

namespace app\commands;

use Exception;
use yii\console\Controller;
use app\services\import\TwoGisReviewService;

/**
 * Import controller
 */
class ImportController extends Controller
{
    /**
     * @return void
     * @throws Exception
     */
    public function actionTwoGisReviews(): void
    {
        (new TwoGisReviewService())->import();
    }
}