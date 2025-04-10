<?php

namespace app\modules\orders\models;

use app\helpers\DebugHelper;

class Users extends \yii\db\ActiveRecord
{

    const EMPTY_ID_LIST = [0];
    public static function tableName(): string
    {
        return 'users';
    }

    /**
     * Возвращает список id пользователей по частичным совпадениям имени и фамилии
     *
     * @param string $name
     * @return array
     */
    public static function getIdListByName(string $name): array
    {
        $name = trim(strtolower($name));
        $name = preg_replace('/\s+/', ' ', $name);

        $query = self::find();
        $query->select(["id"]);
        $query->where(['LIKE', "CONCAT(LOWER(first_name), ' ', LOWER(last_name))", '%' . $name. '%', false]);

        $results = $query->asArray()->all();

        if(!empty($results)) {
            return array_column($results, 'id');
        }

        return self::EMPTY_ID_LIST;
    }
}