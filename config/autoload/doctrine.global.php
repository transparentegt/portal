<?php
return ['doctrine' => ['connection' => ['orm_default' => [
    'driverClass'  =>'Doctrine\DBAL\Driver\PDOMySql\Driver',
    'params'            => [
        'host'          => 'localhost',
        'user'          => '',
        'password'      => '',
        'dbname'        => '',
        'charset'       => 'utf8',
        'driverOptions' => [1002 => 'SET NAMES utf8']
    ]
]]]];