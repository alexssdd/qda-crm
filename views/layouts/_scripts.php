<?php

use yii\helpers\Json;
use app\core\helpers\CareHelper;
use app\core\helpers\DeliveryHelper;

$config = [
    'delivery_pickup' => DeliveryHelper::DELIVERY_PICKUP,
    'delivery_standard' => DeliveryHelper::DELIVERY_STANDARD,
    'delivery_express' => DeliveryHelper::DELIVERY_EXPRESS,
];
$messages = [
    'Select value' => Yii::t('app', 'Select value'),
];
$careHelper = [
    'category_delivery' => CareHelper::CATEGORY_DELIVERY,
    'category_store' => CareHelper::CATEGORY_STORE,
    'complaint_reasons' => CareHelper::getComplaintReasons(),
    'solution_measures_positive' => CareHelper::getSolutionMeasureArray()[CareHelper::TYPE_POSITIVE],
    'solution_measures_negative' => CareHelper::getSolutionMeasureArray()[CareHelper::TYPE_NEGATIVE]
];

?>
<script>
    window.Config = <?= Json::encode($config) ?>;
    window.Messages = <?= Json::encode($messages) ?>;
    window.CareHelper = <?= Json::encode($careHelper) ?>;
</script>