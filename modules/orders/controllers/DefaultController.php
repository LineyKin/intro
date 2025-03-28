<?php

namespace app\modules\orders\controllers;

use yii\web\Controller;

class DefaultController extends Controller
{
    public function actionIndex(): string
    {
        return $this->render('index', [
            'message' => 'list of orders'
        ]);
    }
}