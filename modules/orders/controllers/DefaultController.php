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
    const SEARCH_TYPE_PARAM = 'search-type';

    public $layout = 'main';
    public function actionIndex(): string
    {
        Yii::$app->language = 'en'; // Вместо en-US
        if (Yii::$app->session->has('language')) {
            Yii::$app->language = Yii::$app->session->get('language');
        }

        $params = Yii::$app->request->queryParams;

        //DebugHelper::pr($params,1);

        $model = new Orders();

        if (isset($params[self::SEARCH_TYPE_PARAM])) {
            $model->scenario = $params[self::SEARCH_TYPE_PARAM];
            $model->search = $params['search'];
        }

        if(!$model->validate()) {
            DebugHelper::pr($model->errors,1);
        }

        $q = $model->getQuery($params);
        $data = $q->asArray()->all();


        return $this->render('index', [
            'data' => $data,
        ]);
    }

    public function actionChangeLanguage($lang)
    {
        Yii::$app->session->set('language', $lang);

        return $this->redirect(Yii::$app->request->referrer);
    }
}