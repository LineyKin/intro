<?php

namespace app\modules\orders\models;

use app\helpers\DebugHelper;
use yii\db\ActiveQuery;

/**
 * Возвращает запрос на выгрузку данных о заказах
 * согласно всем фильтрам, роутам и поисковым параметрам
 */
class OrdersSearch extends Orders
{
    public function getQuery() : ActiveQuery
    {
        $query = self::find();
        $query->select([
            "o.id",
            "CONCAT(u.first_name, ' ', u.last_name) AS user",
            "o.link",
            "o.quantity",
            "s.name AS service",
            "o.service_id",
            "o.status",
            "o.mode",
            "o.created_at",
        ]);
        $query->from("orders o");
        $query->innerJoin("users u", "u.id = o.user_id");
        $query->innerJoin("services s", "s.id = o.service_id");

        if (!is_null($this->status)) {
            $query->andFilterWhere(['o.status' => self::getStatusCode($this->status)]);
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
            $query->andFilterWhere(["IN", "user_id", Users::getIdListByName($this->search)]);
        }

        $query->orderBy("o.id");

        return $query;
    }
}