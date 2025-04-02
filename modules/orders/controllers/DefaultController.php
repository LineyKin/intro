<?php

namespace app\modules\orders\controllers;

use app\helpers\DebugHelper;
use app\modules\orders\models\Orders;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\web\Controller;
use Yii;

class DefaultController extends Controller
{

    public $layout = 'ordersLayout';
    public function actionIndex(): string
    {

        //DebugHelper::pr($_GET);

        //DebugHelper::pr(Yii::$app->language);

        //$lang = 'ru';
        //Yii::$app->session->set('language', $lang);
        //Yii::$app->language = $lang;

       // DebugHelper::pr(Yii::$app->language,1);


        $query = Orders::find();
        if (isset($_GET['status'])) {
            $query->andWhere(['status' => Orders::getStatusCode($_GET['status'])]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100, // количество элементов на странице
                'pageParam' => 'page', // название параметра страницы
                'forcePageParam' => false, // не использовать параметр страницы, если это первая страница
                'pageSizeParam' => 'per-page', // название параметра количества элементов на странице
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionChangeLanguage($lang)
    {
        Yii::$app->session->set('language', $lang);

        Yii::$app->language = $lang;

        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
    }
}