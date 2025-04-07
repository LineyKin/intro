<?php

namespace app\modules\orders\models;

use app\helpers\DebugHelper;
use yii\db\ActiveRecord;

class Orders extends ActiveRecord
{
    public $service_id;
    public $search;
    public $mode;
    public $status;

    private $serviceTotalCount;

    const STATUS_LIST = [
        "pending",
        "inprogress",
        "completed",
        "cancelled",
        "fail",
    ];

    const MODE_LIST = [
        "Manual",
        "Auto",
    ];

    const FILENAME = 'orders.csv';

    const SCENARIO_SEARCH_ID = 1;
    const SCENARIO_SEARCH_LINK = 2;
    const SCENARIO_SEARCH_USER = 3;

    function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['mode', 'status', 'service_id'],
            self::SCENARIO_SEARCH_ID => ['search'],
            self::SCENARIO_SEARCH_LINK => ['search'],
            self::SCENARIO_SEARCH_USER => ['search'],
        ];
    }

    public function rules() {
        return [
            [['mode', 'service_id'], 'integer', 'on' => self::SCENARIO_DEFAULT],
            [['status'], 'string', 'on' => self::SCENARIO_DEFAULT],

            [['search'], 'required', 'on' => self::SCENARIO_SEARCH_ID],
            [['search'], 'integer', 'on' => self::SCENARIO_SEARCH_ID],

            [['search'], 'required', 'on' => self::SCENARIO_SEARCH_LINK],
            [['search'], 'string', 'on' => self::SCENARIO_SEARCH_LINK],

            [['search'], 'required', 'on' => self::SCENARIO_SEARCH_USER],
            [['search'], 'string', 'on' => self::SCENARIO_SEARCH_USER],
        ];
    }

    public static function tableName(): string {
        return 'orders';
    }

    public static function getStatusCode(string $status): int
    {
        return array_flip(self::STATUS_LIST)[$status];
    }

    public function getQuery()
    {
        $query = self::find();
        $query->select([
            "o.id AS ID",
            "CONCAT(u.first_name, ' ', u.last_name) AS User",
            "o.link AS Link",
            "o.quantity AS Quantity",
            "s.name AS Service",
            "o.service_id",
            "o.status AS Status",
            "o.mode AS Mode",
            "o.created_at AS Created",
        ]);
        $query->from("orders o");
        $query->innerJoin("users u", "u.id = o.user_id");
        $query->innerJoin("services s", "s.id = o.service_id");

        if (!is_null($this->status)) {
            $query->andFilterWhere(['o.status' => Orders::getStatusCode($this->status)]);
        }

        //DebugHelper::vd("status " . $this->status);
        //DebugHelper::vd("service_id " . $this->service_id);
        //DebugHelper::vd("mode " . $this->mode,1);

        $query->andFilterWhere(['o.mode' => $this->mode]);
        $query->andFilterWhere(['o.service_id' => $this->service_id]);

        if ($this->scenario == self::SCENARIO_SEARCH_ID) {
            $query->andFilterWhere(["o.id" => $this->search]);
        }

        if ($this->scenario == self::SCENARIO_SEARCH_LINK) {
            $query->andFilterWhere(["link" => trim($this->search)]);
        }

        if ($this->scenario == self::SCENARIO_SEARCH_USER) {
            $query->andFilterWhere(["user_id" => Users::getIdByName($this->search)]);
        }

        return $query;
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
        $query->andFilterWhere(['mode' => $this->mode]);
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
            $final[$item['service_id']] = ['count' => $item['count'], 'name' => $item['name']];
        }

        return $final;
    }

    public function getServiceTotalCount() {
        return $this->serviceTotalCount;
    }
}