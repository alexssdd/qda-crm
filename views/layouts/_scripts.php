<?php

use yii\helpers\Json;
use app\core\helpers\DeliveryHelper;

$config = [
    'delivery_pickup' => DeliveryHelper::DELIVERY_PICKUP,
    'delivery_standard' => DeliveryHelper::DELIVERY_STANDARD,
    'delivery_express' => DeliveryHelper::DELIVERY_EXPRESS,
];
$messages = [
    'Select value' => Yii::t('app', 'Select value'),
];

?>
<script>
    window.Config = <?= Json::encode($config) ?>;
    window.Messages = <?= Json::encode($messages) ?>;
</script>