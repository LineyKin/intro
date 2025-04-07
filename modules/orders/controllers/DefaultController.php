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

        $model = new Orders();

        if (isset($params[self::SEARCH_TYPE_PARAM])) {
            $model->scenario = $params[self::SEARCH_TYPE_PARAM];
            $model->mode = null;
            $model->service_id = null;
        }

        $model->setAttributes($params);

        if(!$model->validate()) {
            DebugHelper::pr($model->errors,1);
        }

        $data = $model->getQuery()->asArray()->all();
        $serviceGroupData = $model->getServiceGroupData();

        return $this->render('index', [
            'data' => $data,
            'serviceGroupData' => $serviceGroupData,
            'serviceTotalCount' => $model->getServiceTotalCount(),
        ]);
    }

    public function actionChangeLanguage($lang)
    {
        Yii::$app->session->set('language', $lang);

        return $this->redirect(Yii::$app->request->referrer);
    }
}