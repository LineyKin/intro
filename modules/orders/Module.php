<?php

namespace app\modules\orders;

use Yii;

class Module extends \yii\base\Module
{

    public function init()
    {
        parent::init();

        // Конфигурация модуля
        Yii::configure($this, require __DIR__ . '/config.php');
    }
}