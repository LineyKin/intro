<?php

namespace app\modules\orders\models;

use app\helpers\DebugHelper;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class Orders extends ActiveRecord
{
    public $service_id;
    public $search;
    public $mode;
    public $status;

    const STATUS_LIST = [
        "pending",
        "inprogress",
        "completed",
        "cancelled",
        "fail",
    ];

    const MODE_MANUAL_CODE = 0;
    const MODE_AUTO_CODE = 1;
    const MODE_LIST = [
        self::MODE_MANUAL_CODE => "Manual",
        self::MODE_AUTO_CODE => "Auto",
    ];

    const SCENARIO_SEARCH_ID = 1;
    const SCENARIO_SEARCH_LINK = 2;
    const SCENARIO_SEARCH_USER = 3;

    function scenarios() : array
    {
        return [
            self::SCENARIO_DEFAULT => ['status', 'service_id', 'mode'],
            self::SCENARIO_SEARCH_ID => ['status', 'search'],
            self::SCENARIO_SEARCH_LINK => ['status', 'search'],
            self::SCENARIO_SEARCH_USER => ['status', 'search'],
        ];
    }

    public function rules() : array
    {
        return [
            [['mode', 'service_id'], 'integer', 'on' => self::SCENARIO_DEFAULT],

            [['search'], 'integer', 'on' => self::SCENARIO_SEARCH_ID],

            [['search'], 'string', 'on' => self::SCENARIO_SEARCH_LINK],

            [['search'], 'integer', 'on' => self::SCENARIO_SEARCH_USER],
        ];
    }

    public static function tableName(): string
    {
        return 'orders';
    }

    public static function getStatusByCode(int $code) : string
    {
        return self::STATUS_LIST[$code] ?? sprintf('Unknown status code: %d', $code);
    }

    public static function getModeByCode(int $code) : string
    {
        return self::MODE_LIST[$code] ?? sprintf('Unknown mode code: %d', $code);
    }

    public static function getStatusCode(string $status): int
    {
        return array_flip(self::STATUS_LIST)[$status];
    }

    public function getQuery() : ActiveQuery
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

        $query->orderBy("o.id");

        return $query;
    }
}