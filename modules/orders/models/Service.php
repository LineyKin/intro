<?php

namespace app\modules\orders\models;

use yii\base\Model;

class Service extends Model
{
    public $mode;
    public $status;

    public  function rules()
    {
        return [
            [['mode'], 'integer'],
            [['status'], 'string'],
        ];

    }
    public function getGroupData()
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

    public function getTotalCount()
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

}