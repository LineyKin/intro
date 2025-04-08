<?php

namespace app\modules\orders\controllers;

use app\helpers\DebugHelper;
use app\modules\orders\models\Orders;
use app\modules\orders\models\Service;
use yii\data\Pagination;
use yii\web\Controller;
use Yii;
use yii\web\Response;

class DefaultController extends Controller
{
    const SEARCH_TYPE_PARAM = 'search-type';
    const ROWS_PER_PAGE = 10;
    const FILENAME = 'orders.csv';

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
        $serviceModel = new Service();

        if (isset($params[self::SEARCH_TYPE_PARAM])) {
            $model->scenario = $params[self::SEARCH_TYPE_PARAM];
        }

        $model->setAttributes($params);
        $serviceModel->setAttributes($params);

        /**
         * TODO проверить валидацию
         */
        if(!$model->validate()) {
            DebugHelper::pr($model->errors,1);
        }

        $query = $model->getQuery();

        /**
         * скачиваем csv-файл
         */
        if (isset($params['download'])) {
            $this->downloadFile($model);
        }

        /**
         * пагинатор
         */
        $pages = new Pagination(['totalCount' => $query->count()]);
        $pages->pageSize = self::ROWS_PER_PAGE;
        $query->offset($pages->offset)->limit($pages->limit);

        return $this->render('index', [
            'data' => $query->asArray()->all(), // табличные данные
            'serviceGroupData' => $serviceModel->getGroupData(), // данные для выпадающего списка в столбце "Сервис"
            'serviceTotalCount' => $serviceModel->getTotalCount(), // для All(...) в выпадающем списке сервисов
            'pages' => $pages // для пагинатора
        ]);
    }

    private function downloadFile($model)
    {
        $data = $model->getQuery()->asArray()->all();
        unset($model);

        $fp = fopen(self::FILENAME, 'w');

        foreach ($data as $key => $row) {
            fputcsv($fp, $row, ',', '"', '');
            unset($data[$key]);
        }

        unset($data);

        fclose($fp);

        header( 'Content-Type: text/csv; charset=utf-8' );
        header('Content-Disposition: attachment; filename=' . self::FILENAME);

        readfile(self::FILENAME);
        exit();
    }

    public function actionChangeLanguage($lang) :Response
    {
        Yii::$app->session->set('language', $lang);

        return $this->redirect(Yii::$app->request->referrer);
    }
}