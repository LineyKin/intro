<?php

/**
 * @var $serviceListItems
 * @var $serviceGroupData
 * @var $data
 * @var $pages
 * @var $validateErrors
 * @var $moduleName
 * @var $paginationCounters
 * @var $disabledMode
 * @var $activeModeId
 */

use app\modules\orders\models\Mode;
use app\modules\orders\models\Orders;
use yii\bootstrap5\Dropdown;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <style>
        .label-default{
            border: 1px solid #ddd;
            background: none;
            color: #333;
            min-width: 30px;
            display: inline-block;
        }
    </style>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<?php
    if(!empty($validateErrors)) {
        $errorBanner =  Html::beginTag('div', ['class' => 'alert alert-danger']);
        $errorBanner .= $validateErrors;
        $errorBanner .= Html::endTag('div');

        echo $errorBanner;
    }
?>

<div class="container-fluid">

    <table class="table order-table">
        <thead>
        <tr>
            <th>ID</th>
            <th><?php echo Yii::t($moduleName, 'User')?></th>
            <th><?php echo Yii::t($moduleName, 'Link')?></th>
            <th><?php echo Yii::t($moduleName, 'Quantity')?></th>
            <th class="dropdown-th">
                <div class="dropdown">
                    <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <?php echo Yii::t($moduleName, 'Service')?>
                        <span class="caret"></span>
                    </button>
                    <?php
                    echo Dropdown::widget([
                        'items' => $serviceListItems,
                        'options' => ['class' => 'dropdown-menu'],
                        'encodeLabels' => false,
                    ]);
                    ?>
                </div>
            </th>
            <th><?php echo Yii::t($moduleName, 'Status')?></th>
            <th class="dropdown-th">
                <div class="dropdown">
                    <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <?php echo Yii::t($moduleName, 'Mode')?>
                        <span class="caret"></span>
                    </button>
                    <?php
                    $items = [
                        [
                            'label' => Yii::t($moduleName, "Mode All"),
                            'url' => Url::current(['mode' => null]),
                            'active' => is_null($activeModeId)
                        ],
                        [
                            'label' => Yii::t($moduleName, Mode::getByCode(Mode::MANUAL_CODE)),
                            'url' => Url::current(['mode' => Mode::MANUAL_CODE]),
                            'disabled' => $disabledMode[Mode::MANUAL_CODE],
                            'active' => $activeModeId === Mode::MANUAL_CODE
                        ],
                        [
                            'label' => Yii::t($moduleName, Mode::getByCode(Mode::AUTO_CODE)),
                            'url' => Url::current(['mode' => Mode::AUTO_CODE]),
                            'disabled' => $disabledMode[Mode::AUTO_CODE],
                            'active' => $activeModeId === Mode::AUTO_CODE
                        ],
                    ];

                    echo Dropdown::widget([
                        'items' => $items,
                        'options' => ['class' => 'dropdown-menu'],
                    ]);
                    ?>
                </div>
            </th>
            <th><?php echo Yii::t($moduleName, 'Created')?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $row) { ?>
        <tr>
            <td><?php echo $row['id']?></td>
            <td><?php echo $row['user']?></td>
            <td class="link"><?php echo $row['link']?></td>
            <td><?php echo $row['quantity']?></td>
            <td class="service">
                <span class="label-id">
                    <?php echo $serviceGroupData[$row['service_id']]['count'] ?>
                </span><?php echo $row['service']?>
            </td>
            <td><?php echo Yii::t($moduleName, Orders::getStatusByCode($row['status']))?></td>
            <td><?php echo Yii::t($moduleName, Mode::getByCode($row['mode']))?></td>
            <td>
                <span class="nowrap"><?php echo date('Y-m-d', $row['created_at'])?></span>
                <span class="nowrap"><?php echo date('H:i:s', $row['created_at'])?></span>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>

    <?php
    ActiveForm::begin(
            [
                'action' => Url::current(['download' => 1]),
                'method' => 'get'
            ]
    );

    echo Html::submitButton( Yii::t($moduleName, 'Download CSV'), ['class' => 'btn btn-primary']);

    ActiveForm::end();
    ?>

    <div class="row">
        <div class="col-sm-8">
            <?php
            echo LinkPager::widget([
                'pagination' => $pages,
                'hideOnSinglePage' => true
            ]);
            ?>
        </div>
        <div class="col-sm-4 pagination-counters">
            <?php echo $paginationCounters; ?>
        </div>
    </div>
</div>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
<html>