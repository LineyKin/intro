<?php

namespace app\modules\orders\models;

use app\helpers\DebugHelper;
use yii\db\ActiveRecord;

class Service extends ActiveRecord
{

    public $service_id;

    public static function tableName() : string {
        return 'services';
    }

    public function rules() : array
    {
        return [
            ['service_id', 'integer']
        ];
    }

    /**
     * Возвращает список услуг
     *
     * @return array
     */
    private function getList() :array
    {
        return self::find()->asArray()->all();
    }

    /**
     *
     * Пополняет сгруппированные данные по услугам и их количеству неактивными позициями.
     *
     * @param array $data
     * @return array
     */
    private function addDisableItems(array $data) :array
    {
        $list = $this->getList();

        foreach ($list as $item) {
            if (!isset($data[$item['id']])) {
                $data[$item['id']] = [
                    'name' => $item['name'],
                    'count' => 0,
                    'disabled' => is_null($this->service_id), // всегда true кроме случая, когда фильтруем сами услуги
                ];
            }
        }

        return $data;
    }

    /**
     * На вход табличные данные
     *
     * Возвращает сгруппированные данные по услугам и их количеству
     *
     * @param array $data
     * @return array
     */
    public function getGroupData(array $data) :array
    {
        $result = [];
        foreach ($data as $item) {
            if(!isset($result[$item['service_id']])) {
                $result[$item['service_id']] = [
                    'name' => $item['service'],
                    'count' => 1,
                    'disabled' => false,
                ];
            } else {
                $result[$item['service_id']]['count']++;
            }
        }

        // сортируем по убыванию
        uasort($result, function($a, $b) {
            return $b['count'] - $a['count'];
        });

        return $this->addDisableItems($result);
    }

    /**
     * На вход табличные данные
     *
     * Возвращает количество всех услуг
     *
     * @param array $data
     * @return int
     */
    private function getTotalCount(array $data) :int
    {
        return array_sum(array_column($data, 'count'));
    }

    /**
     * На вход табличные данные
     *
     * Возвращает название первого элемента из списка фильтров по сервису
     *
     * @param array $data
     * @return string
     */
    public function getTotalLabel(array $data) :string
    {
        return sprintf("All (%s)", $this->getTotalCount($data));
    }

}