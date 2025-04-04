<?php

namespace app\modules\orders\models;

class Users extends \yii\db\ActiveRecord
{
    public static function tableName(): string {
        return 'users';
    }

    public static function getIdByName(string $name): ?int
    {
        $nameArr = explode(" ", $name);

        $query = self::find();
        $query->select(["id"]);
        $query->andWhere(['first_name' => trim($nameArr[0])]);
        $query->andWhere(['last_name' => trim($nameArr[1])]);

        return $query->scalar();
    }
}