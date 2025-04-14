<?php

namespace app\modules\orders\models;

use app\helpers\DebugHelper;
use yii\base\InvalidParamException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

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

    const SCENARIO_SEARCH_ID = 1;
    const SCENARIO_SEARCH_LINK = 2;
    const SCENARIO_SEARCH_USER = 3;

    function scenarios() : array
    {
        return [
            self::SCENARIO_DEFAULT => ['status', 'service_id', 'mode'],
            self::SCENARIO_SEARCH_ID => ['status', 'service_id', 'mode', 'search'],
            self::SCENARIO_SEARCH_LINK => ['status', 'service_id', 'mode', 'search'],
            self::SCENARIO_SEARCH_USER => ['status', 'service_id', 'mode', 'search'],
        ];
    }

    public function rules() : array
    {
        return [

            ['status', 'in', 'range' => self::STATUS_LIST],

            [['mode', 'service_id'], 'integer', 'on' => self::SCENARIO_DEFAULT],

            [['search'], 'integer', 'on' => self::SCENARIO_SEARCH_ID],

            [['search'], 'string', 'on' => self::SCENARIO_SEARCH_LINK],

            [['search', 'service_id'], 'integer', 'on' => self::SCENARIO_SEARCH_USER],
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
     * Возвращает код статуса по его названию
     *
     * @param string $status
     * @return int
     * @throws NotFoundHttpException
     */
    public static function getStatusCode(string $status): int
    {
        $flippedArray = array_flip(self::STATUS_LIST);
        if (!isset($flippedArray[$status])) {
            throw new NotFoundHttpException("Ошибка роутинга");
        }

        return $flippedArray[$status];
    }

    public function getValidationErrorMessage() : string {
        $message = "";
        if (!$this->validate()) {
            foreach ($this->errors as $attr => $error) {
                $message .= sprintf('%s: %s', $attr, implode(', ', $error));
            }
        }

        return $message;
    }
}