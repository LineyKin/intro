<?php

/**
 * @var $serviceTotalLabel
 * @var $serviceGroupData
 * @var $data
 * @var $pages
 * @var $validateErrors
 * @var $moduleName
 * @var $paginationCounters
 * @var $disabledMode
 */

use app\modules\orders\models\Mode;
use app\modules\orders\models\Orders;
use yii\bootstrap5\Dropdown;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
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
        $errorBanner .= json_encode($validateErrors);
        $errorBanner .= Html::endTag('div');

        echo $errorBanner;
    }
?>
<nav class="navbar navbar-fixed-top navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="bs-navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">Orders</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container-fluid">
     <?php
    NavBar::begin([
        'brandLabel' =>  Yii::t($moduleName, 'All orders'),
        'brandUrl' => sprintf("/%s/", $moduleName),
        'options' => ['class' => 'nav navbar-expand-md nav-tabs p-b fixed-top'],
    ]);

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            [
                    'label' => Yii::t($moduleName, Orders::getStatusByCode(Orders::STATUS_PENDING_CODE)),
                    'url' => [Orders::getStatusRouteByCode(Orders::STATUS_PENDING_CODE)]
            ],
            [
                    'label' => Yii::t($moduleName, Orders::getStatusByCode(Orders::STATUS_IN_PROGRESS_CODE)),
                    'url' => [Orders::getStatusRouteByCode(Orders::STATUS_IN_PROGRESS_CODE)]
            ],
            [
                    'label' => Yii::t($moduleName, Orders::getStatusByCode(Orders::STATUS_COMPLETE_CODE)),
                    'url' => [Orders::getStatusRouteByCode(Orders::STATUS_COMPLETE_CODE)]
            ],
            [
                    'label' => Yii::t($moduleName, Orders::getStatusByCode(Orders::STATUS_CANCEL_CODE)),
                    'url' => [Orders::getStatusRouteByCode(Orders::STATUS_CANCEL_CODE)]
            ],
            [
                    'label' => Yii::t($moduleName, Orders::getStatusByCode(Orders::STATUS_FAIL_CODE)),
                    'url' => [Orders::getStatusRouteByCode(Orders::STATUS_FAIL_CODE)]
            ],
            [
                'label' => Yii::t($moduleName, 'Language'),
                'items' => [
                    ['label' => 'English', 'url' => ['/orders/change-language', 'lang' => 'en']],
                    ['label' => 'Русский', 'url' => ['/orders/change-language', 'lang' => 'ru']],
                ],
            ],
        ]
    ]); ?>

    <li class="pull-right custom-search">
            <form class="form-inline" action=<?php echo $_SERVER['REQUEST_URI']?>>
                <div class="input-group">
                    <input type="text" name="search" class="form-control" value="" placeholder="Search orders" required>
                    <span class="input-group-btn search-select-wrap">

            <select class="form-control search-select" name="search-type">
              <option value=<?php echo Orders::SCENARIO_SEARCH_ID?> selected="">
                  <?php echo Yii::t($moduleName, 'Order ID')?>
              </option>
              <option value=<?php echo Orders::SCENARIO_SEARCH_LINK?>>
                  <?php echo Yii::t($moduleName, 'Link')?>
              </option>
              <option value=<?php echo Orders::SCENARIO_SEARCH_USER?>>
                    <?php echo Yii::t($moduleName, 'Username')?>
              </option>
            </select>

            <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
            </span>
                </div>
            </form>
    </li>
   <?php NavBar::end();?>

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
                    $items = [
                        [
                            'label' => $serviceTotalLabel,
                            'url' => Url::current(['service_id' => null]),
                        ],
                    ];

                    foreach ($serviceGroupData as $serviceId => $row) {
                        $item = [
                                'label' => sprintf('<span class="label-id">%s</span>  %s', $row['count'], $row['name']),
                            'url' => Url::current(['service_id' => $serviceId]),
                        ];

                        array_push($items, $item);
                    }

                    echo Dropdown::widget([
                        'items' => $items,
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
                        ],
                        [
                            'label' => Yii::t($moduleName, Mode::getByCode(Mode::MANUAL_CODE)),
                            'url' => Url::current(['mode' => Mode::MANUAL_CODE]),
                            'disabled' => $disabledMode[Mode::MANUAL_CODE],
                        ],
                        [
                            'label' => Yii::t($moduleName, Mode::getByCode(Mode::AUTO_CODE)),
                            'url' => Url::current(['mode' => Mode::AUTO_CODE]),
                            'disabled' => $disabledMode[Mode::AUTO_CODE],
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