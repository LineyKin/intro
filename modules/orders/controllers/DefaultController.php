<?php

namespace app\modules\orders\controllers;

use app\helpers\DebugHelper;
use yii\web\Controller;

class DefaultController extends Controller
{

    public $layout = 'ordersLayout';
    public function actionIndex(): string
    {
        return $this->render('index', ['message' => 'all orders']);
    }

    public function actionPending(): string
    {
        return $this->render('pending', ['message' => 'pending']);
    }

    public function actionInProgress(): string
    {
        return $this->render('in_progress', ['message' => 'in_progress']);
    }

    public function actionCompleted(): string
    {
        return $this->render('completed', ['message' => 'completed']);
    }

    public function actionCancelled(): string
    {
        return $this->render('cancelled', ['message' => 'cancelled']);
    }

    public function actionFail(): string
    {
        return $this->render('fail', ['message' => 'fail']);
    }
}