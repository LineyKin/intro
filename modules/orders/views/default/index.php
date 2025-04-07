<?php

use app\modules\orders\models\Orders;
use yii\bootstrap5\Dropdown;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Url;
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
    $category = 'orders';
    NavBar::begin([
        'brandLabel' => Yii::t($category, 'All orders'),
        'brandUrl' => '/orders/',
        'options' => ['class' => 'nav navbar-expand-md nav-tabs p-b fixed-top']
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => [
            ['label' => Yii::t($category, 'Pending'), 'url' => ['/orders/pending']],
            ['label' => Yii::t($category, 'In progress'), 'url' => ['/orders/inprogress']],
            ['label' => Yii::t($category, 'Completed'), 'url' => ['/orders/completed']],
            ['label' =>  Yii::t($category, 'Cancelled'), 'url' => ['/orders/cancelled']],
            ['label' =>  Yii::t($category, 'Fail'), 'url' => ['/orders/fail']],
            [
                'label' => Yii::t('app', 'Language'),
                'items' => [
                    ['label' => 'English', 'url' => ['/orders/change-language', 'lang' => 'en']],
                    ['label' => 'Русский', 'url' => ['/orders/change-language', 'lang' => 'ru']],
                ],
            ],
        ]
    ]); ?>

    <li class="pull-right custom-search">
            <form class="form-inline" action=<?php $_SERVER['REQUEST_URI']?>>
                <div class="input-group">
                    <input type="text" name="search" class="form-control" value="" placeholder="Search orders">
                    <span class="input-group-btn search-select-wrap">

            <select class="form-control search-select" name="search-type">
              <option value="1" selected=""><?php echo Yii::t($category, 'Order ID')?></option>
              <option value="2"><?php echo Yii::t($category, 'Link')?></option>
              <option value="3"><?php echo Yii::t($category, 'Username')?></option>
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
            <th><?php echo Yii::t($category, 'User')?></th>
            <th><?php echo Yii::t($category, 'Link')?></th>
            <th><?php echo Yii::t($category, 'Quantity')?></th>
            <th class="dropdown-th">
                <div class="dropdown">
                    <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <?php echo Yii::t($category, 'Service')?>
                        <span class="caret"></span>
                    </button>
                    <?php
                    $items = [
                        [
                            'label' => sprintf("All (%s)", $serviceTotalCount),
                            'url' => Url::current(['mode' => null]),
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
            <th><?php echo Yii::t($category, 'Status')?></th>
            <th class="dropdown-th">
                <div class="dropdown">
                    <button class="btn btn-th btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <?php echo Yii::t($category, 'Mode')?>
                        <span class="caret"></span>
                    </button>
                    <?php
                    $items = [
                        [
                            'label' => 'All',
                            'url' => Url::current(['mode' => null]),
                        ],
                        [
                            'label' => 'Manual',
                            'url' => Url::current(['mode' => 0]),
                        ],
                        [
                            'label' => 'Auto',
                            'url' => Url::current(['mode' => 1]),
                        ],
                    ];

                    echo Dropdown::widget([
                        'items' => $items,
                        'options' => ['class' => 'dropdown-menu'],
                    ]);
                    ?>
                </div>
            </th>
            <th><?php echo Yii::t($category, 'Created')?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $row) { ?>
        <tr>
            <td><?php echo $row['ID']?></td>
            <td><?php echo $row['User']?></td>
            <td class="link"><?php echo $row['Link']?></td>
            <td><?php echo $row['Quantity']?></td>
            <td class="service">
                <span class="label-id">
                    <?php echo $serviceGroupData[$row['service_id']]['count'] ?>
                </span><?php echo $row['Service']?>
            </td>
            <td><?php echo Orders::STATUS_LIST[$row['Status']]?></td>
            <td><?php echo Orders::MODE_LIST[$row['Mode']]?></td>
            <td>
                <span class="nowrap"><?php echo date('Y-m-d', $row['Created'])?></span>
                <span class="nowrap"><?php echo date('H:i:s', $row['Created'])?></span>
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>
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
            <?php echo sprintf('%s to %s of %s', $pages->page * $pages->pageSize + 1, ($pages->page+1) * $pages->pageSize, $pages->totalCount); ?>
        </div>

    </div>
</div>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
<html>