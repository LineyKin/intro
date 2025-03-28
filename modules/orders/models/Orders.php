<?php

namespace app\modules\orders\models;

use Yii;
class Orders extends \yii\base\Model
{
    const STATUS_LIST = [
        "Pending",
        "In progress",
        "Completed",
        "Cancelled",
        "Fail",
    ];

    const MOD_LIST = [
        "Manual",
        "Auto",
    ];

    public function getList(): array
    {
        return [];
    }
}