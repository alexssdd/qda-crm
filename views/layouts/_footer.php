<?php

use yii\web\View;
use app\core\helpers\UserHelper;

/** @var $this View */

?>
<div class="modal modal-main" role="alert"></div>
<div class="modal modal-additional" role="alert"></div>
<?= $this->render('_scripts') ?>
<?php

$js = <<<JS

AutoLogout.run();

JS;

if (UserHelper::getIdentity()->role == UserHelper::ROLE_OPERATOR){
    $this->registerJs($js);
}

?>