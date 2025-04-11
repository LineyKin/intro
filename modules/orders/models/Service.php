<?php

namespace app\modules\orders\models;

use app\helpers\DebugHelper;
use yii\db\ActiveRecord;

class Service extends ActiveRecord
{

    public $service_id;
    public $searchType;
    public $search;
    public $status;
    public $mode;

    public static function tableName() : string {
        return 'services';
    }

    public function rules() : array
    {
        return [
            [['service_id', 'status', 'search', 'searchType', 'mode'], 'safe']
        ];
    }

    /**
     * Возвращает список услуг
     *
     * @return array
     */
    private function getList() :array
    {
        return self::find()->asArray()->all();
    }

    /**
     *
     * Пополняет сгруппированные данные по услугам и их количеству неактивными позициями.
     *
     * @param array $data
     * @return array
     */
    private function addDisableItems(array $data) :array
    {
        $list = $this->getList();

        foreach ($list as $item) {
            if (!isset($data[$item['id']])) {
                $data[$item['id']] = [
                    'name' => $item['name'],
                    'count' => 0,
                    'disabled' => true
                ];
            }
        }

        return $data;
    }

    public function getGroupData() :array
    {
        $query = self::find();
        $query->select([
            "s.id AS service_id",
            "s.name AS service",
            "COUNT(*) AS count",
        ]);

        $query->from("services s");
        $query->innerJoin("orders o", "o.service_id = s.id");
        $query->groupBy("o.service_id");
        $query->orderBy("count DESC");

        if (!is_null($this->status)) {
            $query->andFilterWhere(['o.status' => Orders::getStatusCode($this->status)]);
        }

        $query->andFilterWhere(['o.mode' => $this->mode]);

        if ($this->searchType == Orders::SCENARIO_SEARCH_ID) {
            $query->andFilterWhere(["o.id" => $this->search]);
        }

        if ($this->searchType == Orders::SCENARIO_SEARCH_LINK) {
            $query->andFilterWhere(["link" => trim($this->search)]);
        }

        if ($this->searchType == Orders::SCENARIO_SEARCH_USER) {
            $query->andFilterWhere(["IN", "user_id", Users::getIdListByName($this->search)]);
        }

        $data = $query->asArray()->all();

        $result = [];
        foreach ($data as $item) {
            $result[$item['service_id']] = [
                'name' => $item['service'],
                'count' => $item['count'],
                'disabled' => false,
            ];
        }

        return $this->addDisableItems($result);
    }


    /**
     * На вход сгруппированные данные по услугами и их количеству
     *
     * Возвращает количество всех услуг
     *
     * @param array $data
     * @return int
     */
    private function getTotalCount(array $data) :int
    {
        return array_sum(array_column($data, 'count'));
    }

    /**
     * На вход сгруппированные данные по услугами и их количеству
     *
     * Возвращает название первого элемента из списка фильтров по сервису
     *
     * @param array $data
     * @return string
     */
    public function getTotalLabel(array $data) :string
    {
        return sprintf("All (%s)", $this->getTotalCount($data));
    }

}