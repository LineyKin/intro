<?php

namespace app\modules\orders\models;

use Yii;
use yii\db\ActiveRecord;
class Orders extends ActiveRecord
{
    public $ID;
    public $User;
    public $Link;
    public $Quantity;
    public $Service;
    public $service_id;
    public $Status;
    public $Mode;
    public $Created;

    public $search;

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

    const SCENARIO_SEARCH_ID = 1;
    const SCENARIO_SEARCH_LINK = 2;
    const SCENARIO_SEARCH_USER = 3;

    function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['Mode', 'service_id'],
            self::SCENARIO_SEARCH_ID => ['search'],
            self::SCENARIO_SEARCH_LINK => ['search'],
            self::SCENARIO_SEARCH_USER => ['search'],
        ];
    }

    public function rules() {
        return [
            [['Mode', 'service_id'], 'integer'],

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

    public function getQuery($params)
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

        if (isset($params['status'])) {
            $query->andFilterWhere(['o.status' => Orders::getStatusCode($params['status'])]);
        }

        if (isset($params['mode'])) {
            $query->andFilterWhere(['o.mode' => $params['mode']]);
        }

        if ($this->scenario == self::SCENARIO_SEARCH_ID) {
            $query->andFilterWhere(["o.id" => $this->search]);
        }

        if ($this->scenario == self::SCENARIO_SEARCH_LINK) {
            $query->andFilterWhere(["link" => $this->search]);
        }

        if ($this->scenario == self::SCENARIO_SEARCH_USER) {
            $query->andFilterWhere(["user_id" => Users::getIdByName($this->search)]);
        }


        // заглушка на время разработки
        $query->limit(10);

        return $query;
    }
}