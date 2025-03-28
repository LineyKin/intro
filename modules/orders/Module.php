<?php

namespace app\modules\orders;

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\orders\controllers';
    public function init()
    {
        parent::init();

        // Инициализация модуля
        $this->setAliases([
            '@orders' => '@app/modules/orders',
        ]);

        // Конфигурация модуля
        Yii::configure($this, require __DIR__ . '/config.php');
    }
}