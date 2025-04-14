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
use yii\helpers\Url;

class DefaultController extends Controller
{
    const SEARCH_TYPE_PARAM = 'search-type';
    const FILENAME = 'orders.csv';

    public $layout = 'main';

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

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
            'serviceGroupData' => $serviceGroupData,
            'serviceListItems' => $this->buildServiceListItems($serviceGroupData, $service->getTotalLabel($serviceGroupData), $service->service_id),
            'pages' => $pages, // для пагинатора
            'validateErrors' => $order->getValidationErrorMessage(),
            'moduleName' => $this->module->id,
            'disabledMode' => $mode->getDisabled(),
            'activeModeId' => $mode->getCode(),
            'paginationCounters' => $pages->getPaginationCounters(),
        ]);
    }


    /**
     *
     * собирает массив-аргумент в Dropdown::widget во view
     *
     * @param array $serviceGroupData
     * @param string $serviceTotalLabel
     * @param int|null $serviceId
     * @return array[]
     */
    private function buildServiceListItems(array $serviceGroupData, string $serviceTotalLabel, int|null $serviceId)  :array
    {
        $items = [
            [
                'label' => $serviceTotalLabel,
                'url' => Url::current(['service_id' => null]),
                'active' => is_null($serviceId)
            ],
        ];

        foreach ($serviceGroupData as $id => $row) {
            $item = [
                'label' => sprintf('<span class="label-id">%s</span>  %s', $row['count'], $row['name']),
                'url' => Url::current(['service_id' => $id]),
                'disabled' => $row['disabled'],
                'active' => $id == $serviceId,
            ];

            $items[] = $item;
        }

        return $items;
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