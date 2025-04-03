<?php

namespace app\modules\orders\models;

use app\helpers\DebugHelper;
use app\modules\orders\models\Orders;
use yii\data\ActiveDataProvider;

class OrdersSearch extends Orders
{
    private $status;

    public function setStatus($status)
    {
        $this->status = $status;
    }
    public function rules() {
        return [
            [['Mode'], 'integer'],
        ];
    }

    public function search($params) {
        $query = Orders::getQuery();
        if (isset($this->status)) {
            $query->andWhere(['status' => Orders::getStatusCode($this->status)]);
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

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['mode' => $this->Mode]);

        return $dataProvider;
    }
}