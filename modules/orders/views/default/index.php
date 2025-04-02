<?php

use yii\grid\GridView;

?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'ID',
        'User',
        'Link',
        'Quantity',
        'Service',
        [
            'attribute' => 'Status',
            'value' => function ($model) {
                return $model::STATUS_LIST[$model->Status] ?? $model->Status;
            }
        ],
        [
            'attribute' => 'Mode',
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
        'maxButtonCount' => 5, // максимальное количество кнопок страниц
    ],
]) ?>