<?php

use yii\web\View;
use yii\helpers\Json;
use app\entities\Order;

/* @var $this View */
/* @var $data [] */
/* @var $params [] */
/* @var $order Order */
$this->title = Yii::t('app', 'Widget Page');
?>
<script src="<?= $params['url_js'] ?>"></script>
<script>
    halyk.pay(<?= Json::encode($data) ?>, {success: true});
</script>