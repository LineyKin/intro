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

    public $layout = 'ordersLayout';
    public function actionIndex(): string
    {
        Yii::$app->language = 'en'; // Вместо en-US
        if (Yii::$app->session->has('language')) {
            Yii::$app->language = Yii::$app->session->get('language');
        }

        $searchModel = new OrdersSearch();
        $searchModel->setStatus(!empty($_GET['status']) ? $_GET['status'] : null);

        if (isset($_GET['searchType'])) {
            $searchModel->scenario = $_GET['searchType'];
        }

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionChangeLanguage($lang)
    {
        Yii::$app->session->set('language', $lang);

        return $this->redirect(Yii::$app->request->referrer);
    }
}