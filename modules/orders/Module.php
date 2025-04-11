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

        Yii::$app->i18n->translations['orders*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'basePath' => '@app/modules/orders/messages',
            'fileMap' => [
                'orders' => 'orders.php',
            ],
        ];
    }
}