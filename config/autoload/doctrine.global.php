<?php
return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass'  =>'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params'       => array(
                    'host'     => 'localhost',
                    'user'     => '',
                    'password' => '',
                    'dbname'   => '',
                    'charset'  => 'utf8',
                    'driverOptions' => [
                            1002 => 'SET NAMES utf8'
                    ]
)))));