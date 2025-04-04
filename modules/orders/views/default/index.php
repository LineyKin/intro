<?php

use app\modules\orders\models\Orders;
use yii\bootstrap5\Html;
use yii\grid\GridView;

?>

<?php
$form = Html::beginForm([explode("?", $_SERVER['REQUEST_URI'])[0]], 'get', ['class' => 'form-inline my-2 my-lg-0']);
$form .= Html::textInput('value', '', [
    'class' => 'form-control mr-sm-2',
    'placeholder' => "Search",
    'aria-label' => 'Search'
]);
$form .= Html::dropDownList('type', '', [
    'id' => "Order ID",
    'link' => "Link",
    'user' => "Username",
], [
    'class' => 'form-control mr-sm-2'
]);
$form .= Html::submitButton("Search", ['class' => 'btn btn-primary'], [
]);
$form .= Html::endForm();

echo $form;

?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        'ID',
        'User',
        'Link',
        'Quantity',
        [
            'attribute' => 'Service',
            'filter' => \yii\helpers\Html::activeDropDownList(
                $searchModel,
                'service_id',
                $searchModel->getServiceGroupData(),
                [
                    'class' => 'form-control',
                    'prompt' => sprintf('All (%s)', $searchModel->getServiceTotalCount()),
                ]
            ),
        ],
        [
            'attribute' => 'Status',
            'value' => function ($model) {
                return $model::STATUS_LIST[$model->Status] ?? $model->Status;
            }
        ],
        [
            'attribute' => 'Mode',
            'filter' => \yii\helpers\Html::activeDropDownList(
                $searchModel,
                'Mode',
                Orders::MODE_LIST,
                [
                    'class' => 'form-control',
                    'prompt' => 'All',
                ]
            ),
            'value' => function ($model) {
                return $model::MODE_LIST[$model->Mode] ?? $model->Mode;
            }
        ],
        [
            'attribute' => 'Created',
            'format' => 'datetime'
        ],
    ],
    'pager' => [
        'options' => ['class' => 'pagination'], // класс для пагинации
        'prevPageLabel' => '&laquo;', // текст для кнопки "предыдущая страница"
        'nextPageLabel' => '&raquo;', // текст для кнопки "следующая страница"
        'firstPageLabel' => 'Первая', // текст для кнопки "первая страница"
        'lastPageLabel' => 'Последняя', // текст для кнопки "последняя страница"
        'maxButtonCount' => 10, // максимальное количество кнопок страниц
    ],
]) ?>