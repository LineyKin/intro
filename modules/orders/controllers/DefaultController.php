<?php

namespace app\modules\orders\controllers;

use app\helpers\DebugHelper;
use app\modules\orders\models\Orders;
use yii\data\Pagination;
use yii\web\Controller;
use Yii;

class DefaultController extends Controller
{
    const SEARCH_TYPE_PARAM = 'search-type';
    const ROWS_PER_PAGE = 10;

    public $layout = 'main';

    public function actionIndex(): string
    {
        /**
         * язык
         */
        Yii::$app->language = 'en'; // Вместо en-US
        if (Yii::$app->session->has('language')) {
            Yii::$app->language = Yii::$app->session->get('language');
        }

        $params = Yii::$app->request->queryParams;
        $model = new Orders();

        if (isset($params[self::SEARCH_TYPE_PARAM])) {
            $model->scenario = $params[self::SEARCH_TYPE_PARAM];
        }


        $model->setAttributes($params);

        /**
         * TODO проверить валидацию
         */
        if(!$model->validate()) {
            DebugHelper::pr($model->errors,1);
        }

        $query = $model->getQuery();
        $serviceGroupData = $model->getServiceGroupData();

        /**
         * скачиваем csv-файл
         */
        if (isset($params['download'])) {
            $this->downloadFile($model);
        }

        /**
         * пагинатор
         */
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = self::ROWS_PER_PAGE;
        $query->offset($pages->offset)->limit($pages->limit);

        return $this->render('index', [
            'data' => $query->asArray()->all(),
            'serviceGroupData' => $serviceGroupData,
            'serviceTotalCount' => $model->getServiceTotalCount(),
            'pages' => $pages,
        ]);
    }

    private function downloadFile($model)
    {
        // заглушка лимит=10 на время разработки
        $data = $model->getQuery()->limit(10)->asArray()->all();
        $fileName = $model::FILENAME;
        unset($model);

        $fp = fopen($fileName, 'w');

        foreach ($data as $fields) {
            fputcsv($fp, $fields, ',', '"', '');
        }

        unset($data);

        fclose($fp);

        header( 'Content-Type: text/csv; charset=utf-8' );
        header('Content-Disposition: attachment; filename=' . $fileName);

        readfile($fileName);
        exit();
    }

    public function actionChangeLanguage($lang)
    {
        Yii::$app->session->set('language', $lang);

        return $this->redirect(Yii::$app->request->referrer);
    }
}