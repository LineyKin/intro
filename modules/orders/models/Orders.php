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
    public $Status;
    public $Mode;
    public $Created;

    const STATUS_LIST = [
        "Pending",
        "In progress",
        "Completed",
        "Cancelled",
        "Fail",
    ];

    const STATUS_ROUT_LIST = [
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

    public static function tableName(): string {
        return 'orders';
    }

    public  static function getStatusCode(string $status): int
    {
        return array_flip(self::STATUS_ROUT_LIST)[$status];
    }

    public static function getQuery()
    {
        $query = self::find();
        $query->select([
            "o.id AS ID",
            "CONCAT(u.first_name, ' ', u.last_name) AS User",
            "o.link AS Link",
            "o.quantity AS Quantity",
            "s.name AS Service",
            "o.status AS Status",
            "o.mode AS Mode",
            "o.created_at AS Created",
        ]);
        $query->from("orders o");
        $query->innerJoin("users u", "u.id = o.user_id");
        $query->innerJoin("services s", "s.id = o.service_id");

        return $query;
    }
}