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


    const STATUS_PENDING_CODE = 0;
    const STATUS_IN_PROGRESS_CODE = 1;
    const STATUS_COMPLETE_CODE = 2;
    const STATUS_CANCEL_CODE = 3;
    const STATUS_FAIL_CODE = 4;
    const STATUS_LIST = [
        self::STATUS_PENDING_CODE => "pending",
        self::STATUS_IN_PROGRESS_CODE => "inprogress",
        self::STATUS_COMPLETE_CODE => "completed",
        self::STATUS_CANCEL_CODE => "cancelled",
        self::STATUS_FAIL_CODE => "fail",
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

    /**
     * Возвращает статус по коду
     *
     * @param int $code
     * @return string
     */
    public static function getStatusByCode(int $code) : string
    {
        return self::STATUS_LIST[$code] ?? sprintf('Unknown status code: %d', $code);
    }

    /**
     * Возвращает режим по коду
     *
     * @param int $code
     * @return string
     */
    public static function getModeByCode(int $code) : string
    {
        return self::MODE_LIST[$code] ?? sprintf('Unknown mode code: %d', $code);
    }

    /**
     * Возвращает код статуса по его названию
     *
     * @param string $status
     * @return int
     */
    public static function getStatusCode(string $status): int
    {
        return array_flip(self::STATUS_LIST)[$status];
    }

    public static function getStatusRouteByCode(int $code) : string
    {
        return sprintf('/orders/%s', self::getStatusByCode($code));
    }
}