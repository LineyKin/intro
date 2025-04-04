<?php

namespace app\modules\orders\models;

use app\helpers\DebugHelper;

use yii\data\ActiveDataProvider;

class OrdersSearch extends Orders
{
    private $status;
    private $serviceTotalCount;

    public $searchValue;

    public function setStatus($status)
    {
        $this->status = $status;
    }

    const SCENARIO_SEARCH_ID = "id";
    const SCENARIO_SEARCH_LINK = "link";
    const SCENARIO_SEARCH_USER = "user";

    function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['Mode', 'service_id'],
            self::SCENARIO_SEARCH_ID => ['searchValue'],
            self::SCENARIO_SEARCH_LINK => ['searchValue'],
            self::SCENARIO_SEARCH_USER => ['searchValue'],
        ];
    }

    public function rules() {
        return [
            [['Mode', 'service_id'], 'integer'],

            [['searchValue'], 'required', 'on' => self::SCENARIO_SEARCH_ID],
            [['searchValue'], 'integer', 'on' => self::SCENARIO_SEARCH_ID],

            [['searchValue'], 'required', 'on' => self::SCENARIO_SEARCH_LINK],
            [['searchValue'], 'string', 'on' => self::SCENARIO_SEARCH_LINK],

            [['searchValue'], 'required', 'on' => self::SCENARIO_SEARCH_USER],
            [['searchValue'], 'string', 'on' => self::SCENARIO_SEARCH_USER],
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
        $this->setAttributes($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['mode' => $this->Mode]);
        $query->andFilterWhere(['service_id' => $this->service_id]);


        if ($this->scenario == self::SCENARIO_SEARCH_ID) {
            $query->andFilterWhere(["o.id" => $this->searchValue]);
        }

        if ($this->scenario == self::SCENARIO_SEARCH_LINK) {
            $query->andFilterWhere(["link" => $this->searchValue]);
        }

        if ($this->scenario == self::SCENARIO_SEARCH_USER) {
            $query->andFilterWhere(["user_id" => Users::getIdByName($this->searchValue)]);
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