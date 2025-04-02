<?php

namespace app\modules\orders\models;

use Yii;
use yii\db\ActiveRecord;
class Orders extends ActiveRecord
{
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

    const MOD_LIST = [
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

    public function getAllOrders(): array
    {
        $sql = "SELECT
	                o.id AS ID,
	                CONCAT(u.first_name, ' ', u.last_name) AS User,
	                o.link AS Link,
	                o.quantity AS Quantity,
	                s.name AS Service,
	                o.status AS Status,
	                o.mode AS Mode,
	                o.created_at AS Created
                FROM orders AS o
                INNER JOIN users AS u ON u.id = o.user_id
                INNER JOIN services AS s ON o.service_id = s.id";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }
}