<?php

namespace app\modules\orders\models;

use app\helpers\DebugHelper;
use yii\base\Model;

class Mode extends Model
{
    public $mode;
    public $service_id;
    public $searchType;
    public $search;
    public $status;

    const MANUAL_CODE = 0;
    const AUTO_CODE = 1;
    const LIST = [
        self::MANUAL_CODE => "Manual",
        self::AUTO_CODE => "Auto",
    ];

    public function rules() : array
    {
        return [
            [['service_id', 'status', 'search', 'searchType', 'mode'], 'safe']
        ];
    }

    public function getCode() :int|null
    {
        return is_null($this->mode) ? null : (int) $this->mode;
    }

    /**
     * Возвращает режим по коду
     *
     * @param int $code
     * @return string
     */
    public static function getByCode(int $code) : string
    {
        return self::LIST[$code] ?? sprintf('Unknown mode code: %d', $code);
    }

    /**
     * Возвращает список активных и неактивных пунктов в фильтре
     *
     * @param array $data
     * @return array
     */
    public function getDisabled() :array
    {
        $query = Orders::find();
        $query->select([
            "mode",
        ]);
        $query->groupBy("mode");

        if (!is_null($this->status)) {
            $query->andFilterWhere(['status' => Orders::getStatusCode($this->status)]);
        }

        $query->andFilterWhere(['service_id' => $this->service_id]);

        if ($this->searchType == Orders::SCENARIO_SEARCH_ID) {
            $query->andFilterWhere(["id" => $this->search]);
        }

        if ($this->searchType == Orders::SCENARIO_SEARCH_LINK) {
            $query->andFilterWhere(["link" => trim($this->search)]);
        }

        if ($this->searchType == Orders::SCENARIO_SEARCH_USER) {
            $query->andFilterWhere(["IN", "user_id", Users::getIdListByName($this->search)]);
        }

        $result = $query->asArray()->all();
        $result = array_column($result, 'mode');

        $final = [];

        foreach (self::LIST as $code => $mode) {
            $final[$code] = !in_array($code, $result);
        }

        return $final;
    }
}