<?php

namespace app\modules\orders\controllers;

use app\helpers\DebugHelper;
use app\modules\orders\models\Orders;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\web\Controller;

class DefaultController extends Controller
{

    public $layout = 'ordersLayout';
    public function actionIndex(): string
    {
        $query = Orders::find()->select(['']);

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

    public function actionPending(): string
    {
        $query = Orders::find()->where(['status' => 0]);

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

    public function actionInprogress(): string
    {
        $query = Orders::find()->where(['status' => 1]);

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

    public function actionCompleted(): string
    {
        $query = Orders::find()->where(['status' => 2]);

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

    public function actionCancelled(): string
    {
        $query = Orders::find()->where(['status' => 3]);

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

    public function actionFail(): string
    {
        $query = Orders::find()->where(['status' => 4]);

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
}