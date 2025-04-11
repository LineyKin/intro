<?php

namespace app\modules\orders\controllers;

use app\helpers\DebugHelper;
use app\modules\orders\models\Mode;
use app\modules\orders\models\OrdersPagination;
use app\modules\orders\models\OrdersSearch;
use app\modules\orders\models\Service;
use yii\web\Controller;
use Yii;
use yii\web\Response;

class DefaultController extends Controller
{
    const SEARCH_TYPE_PARAM = 'search-type';
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
        $order = new OrdersSearch();
        $service = new Service();
        $mode = new Mode();

        if (isset($params[self::SEARCH_TYPE_PARAM])) {
            $order->scenario = $params[self::SEARCH_TYPE_PARAM];
            $service->searchType = $params[self::SEARCH_TYPE_PARAM];
            $mode->searchType = $params[self::SEARCH_TYPE_PARAM];
        }

        $order->setAttributes($params);
        $service->setAttributes($params);
        $mode->setAttributes($params);

        $order->validate();
        $mode->validate();

        /**
         * скачиваем csv-файл
         */
        if (isset($params['download'])) {
            $this->downloadFile($order);
        }

        $query = $order->getQuery();
        $serviceGroupData = $service->getGroupData();

        /**
         * пагинатор
         */
        $pages = new OrdersPagination(['totalCount' => $query->count()]);
        $pages->setPerPage($params);
        $query->offset($pages->offset)->limit($pages->limit);

        return $this->render('index', [
            'data' => $query->asArray()->all(), // табличные данные
            'serviceGroupData' => $serviceGroupData, // данные для выпадающего списка в столбце "Сервис"
            'serviceTotalLabel' => $service->getTotalLabel($serviceGroupData),
            'pages' => $pages, // для пагинатора
            'validateErrors' => $order->errors,
            'moduleName' => $this->module->id,
            'disabledMode' => $mode->getDisabled(),
            'paginationCounters' => $pages->getPaginationCounters(),
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