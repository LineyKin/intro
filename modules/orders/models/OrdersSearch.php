<?php

namespace app\modules\orders\models;

use app\helpers\DebugHelper;

use yii\data\ActiveDataProvider;

class OrdersSearch extends Orders
{
    private $status;
    private $serviceTotalCount;

    public function setStatus($status)
    {
        $this->status = $status;
    }
    public function rules() {
        return [
            [['Mode', 'service_id'], 'integer'],
        ];
    }

    public function search($params) {
        $query = Orders::getQuery();
        if (!is_null($this->status)) {
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
        $query->andFilterWhere(['service_id' => $this->service_id]);

        if (!empty($_GET['value'])) {
            switch ($_GET['type']) {
                case 'id':
                    $query->andFilterWhere(["o.id" => $_GET['value']]);
                    break;
                case 'link':
                    $query->andFilterWhere(["link" => $_GET['value']]);
                    break;
                case 'user':
                    $userId = Users::getIdByName($_GET['value']);
                    $query->andFilterWhere(["o.user_id" => $userId]);
            }
        }

        return $dataProvider;
    }

    public function getServiceGroupData()
    {
        $query = self::find();
        $query->select([
            "o.service_id",
            "s.name",
            "COUNT(*) AS count",
        ]);
        $query->from("orders o");
        $query->innerJoin("services s", "s.id = o.service_id");
        $query->andFilterWhere(['mode' => $this->Mode]);
        if (!is_null($this->status)) {
            $query->andWhere(['status' => Orders::getStatusCode($this->status)]);
        }
        $query->groupBy("o.service_id");
        $query->orderBy('count DESC');
        $query->asArray();

        $data = $query->all();
        unset($query);

        $final = [];
        foreach ($data as $item) {
            $this->serviceTotalCount += $item['count'];
            $final[$item['service_id']] = sprintf("[%s] %s", $item['count'], $item['name']);
        }

        return $final;
    }

    public function getServiceTotalCount() {
        return $this->serviceTotalCount;
    }
}