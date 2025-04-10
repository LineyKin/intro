<?php

namespace app\modules\orders\controllers;

use app\helpers\DebugHelper;
use app\modules\orders\models\OrdersSearch;
use app\modules\orders\models\Service;
use yii\data\Pagination;
use yii\web\Controller;
use Yii;
use yii\web\Response;

class DefaultController extends Controller
{
    const SEARCH_TYPE_PARAM = 'search-type';
    const PER_PAGE_DEFAULT = 10;
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
        $model = new OrdersSearch();
        $serviceModel = new Service();

        if (isset($params[self::SEARCH_TYPE_PARAM])) {
            $model->scenario = $params[self::SEARCH_TYPE_PARAM];
        }

        $model->setAttributes($params);
        $serviceModel->setAttributes($params);

        $model->validate();

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
        $pages->pageSize = $params['per-page'] ?? self::PER_PAGE_DEFAULT;
        $query->offset($pages->offset)->limit($pages->limit);
        $data =  $query->asArray()->all();

        return $this->render('index', [
            'data' => $data, // табличные данные
            'serviceGroupData' => $serviceModel->getGroupData(), // данные для выпадающего списка в столбце "Сервис"
            'pages' => $pages, // для пагинатора
            'validateErrors' => $model->errors,
            'moduleName' => $this->module->id,
            'notDisabledMode' => array_unique(array_column($data, 'mode')),
            'serviceTotalLabel' => sprintf("All (%s)", $serviceModel->getTotalCount()),
            'paginationCounters' => sprintf('%s to %s of %s', $pages->page * $pages->pageSize + 1, ($pages->page + 1) * $pages->pageSize, $pages->totalCount)
        ]);
    }

    /**
     * Скачивает отображённые данные в csv-файл
     *
     * @param $model
     * @return void
     */
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

    /**
     * Смена языка
     *
     * @param $lang
     * @return Response
     */
    public function actionChangeLanguage($lang) :Response
    {
        Yii::$app->session->set('language', $lang);

        return $this->redirect(Yii::$app->request->referrer);
    }
}