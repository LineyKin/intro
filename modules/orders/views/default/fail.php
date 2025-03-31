<?php

use yii\grid\GridView;

?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'user_id',
        'link',
        'quantity',
        'service_id',
        'status',
        'mode',
        'created_at',
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