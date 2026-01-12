<?php

namespace app\modules\location\commands;

use yii\console\Controller;
use app\modules\location\services\ImportService;

class ImportController extends Controller
{
    public function actionCountries(): void
    {
        (new ImportService())->countries();
    }

    public function actionLocations($countryCode): void
    {
        (new ImportService())->locations($countryCode);
    }

    public function actionStructure(): void
    {
        (new ImportService())->structure();
    }
}
