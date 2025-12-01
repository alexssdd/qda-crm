<?php
namespace app\core;

class SerialColumn extends \yii\grid\SerialColumn
{
    /** @var int[] */
    public $options = [
        'width' => 45
    ];
}