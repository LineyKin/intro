<?php

use app\modules\orders\models\Orders;
use yii\grid\GridView;

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