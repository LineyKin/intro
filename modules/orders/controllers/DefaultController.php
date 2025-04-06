<?php

namespace app\modules\orders\controllers;

use app\helpers\DebugHelper;
use app\modules\orders\models\Orders;
use app\modules\orders\models\OrdersSearch;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\web\Controller;
use Yii;

class DefaultController extends Controller
{

    public $layout = 'main';
    public function actionIndex(): string
    {
        Yii::$app->language = 'en'; // Вместо en-US
        if (Yii::$app->session->has('language')) {
            Yii::$app->language = Yii::$app->session->get('language');
        }

        //DebugHelper::pr($_GET,1);

        $message = "good view will be here";

        return $this->render('index', [
            'message' => $message,
        ]);
    }

    public function actionChangeLanguage($lang)
    {
        Yii::$app->session->set('language', $lang);

        return $this->redirect(Yii::$app->request->referrer);
    }
}