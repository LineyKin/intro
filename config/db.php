<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => sprintf("mysql:host=db;dbname=%s", $_ENV['MYSQL_DATABASE']),
    'username' => $_ENV['MYSQL_USER'],
    'password' => $_ENV['MYSQL_PASSWORD'],
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
