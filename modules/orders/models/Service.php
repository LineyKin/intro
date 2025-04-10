<?php

namespace app\modules\orders\models;

use yii\base\Model;

class Service extends Model
{
    public $mode;
    public $status;

    public  function rules() : array
    {
        return [
            [['mode'], 'integer'],
            [['status'], 'string'],
        ];
    }

    /**
     * Возвращает сгруппированные данные по услугам и их количеству
     *
     * @return array
     */
    public function getGroupData() : array
    {
        $query = Orders::find();
        $query->select([
            "o.service_id",
            "s.name",
            "COUNT(*) AS count",
        ]);
        $query->from("orders o");
        $query->innerJoin("services s", "s.id = o.service_id");
        $query->andFilterWhere(['mode' => $this->mode]);
        if (!is_null($this->status)) {
            $query->andFilterWhere(['status' => Orders::getStatusCode($this->status)]);
        }
        $query->groupBy("o.service_id");
        $query->orderBy('count DESC');
        $query->asArray();

        $data = $query->all();
        unset($query);

        $final = [];
        foreach ($data as $item) {
            $final[$item['service_id']] = ['count' => $item['count'], 'name' => $item['name']];
        }

        return $final;
    }

    /**
     * Возвращает количество всех услуг
     *
     * @return int
     */
    private function getTotalCount() :int
    {
        $query = Orders::find();
        $query->select([
            "COUNT(*) AS count",
        ]);
        $query->from("orders");
        $query->andFilterWhere(['mode' => $this->mode]);
        if (!is_null($this->status)) {
            $query->andFilterWhere(['status' => Orders::getStatusCode($this->status)]);
        }

        $query->asArray();

        $data = $query->all();
        unset($query);

        return $data[0]['count'];
    }

    /**
     * Возвращает название первого элемента из списка фильтров по сервису
     *
     * @return string
     */
    public function getTotalLabel() :string
    {
        return sprintf("All (%s)", $this->getTotalCount());
    }

}