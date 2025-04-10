<?php

namespace app\modules\orders\models;

use app\helpers\DebugHelper;
use yii\base\Model;

class Mode extends Model
{
    public $mode;

    const MANUAL_CODE = 0;
    const AUTO_CODE = 1;
    const LIST = [
        self::MANUAL_CODE => "Manual",
        self::AUTO_CODE => "Auto",
    ];

    public function rules()
    {
        return [
            [['mode'], 'integer'],
        ];
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
     * Принимает на вход табличные данные заказов.
     * Возвращает список активных и неактивных пунктов в фильтре
     *
     * @param array $data
     * @return array
     */
    public function getDisabled(array $data) :array
    {
        $availableMode = array_unique(array_column($data, 'mode'));

        $res = [];
        foreach (self::LIST as $code => $mode) {
            $res[$code] = !in_array($code, $availableMode) && is_null($this->mode);
        }

        return $res;
    }
}