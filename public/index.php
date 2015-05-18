<?php
 /**
  * Display all errors when APPLICATION_ENV is development.
  */
if (!empty($_SERVER['APPLICATION_ENV']) && $_SERVER['APPLICATION_ENV'] == 'development') {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}

date_default_timezone_set('America/Guatemala');

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

// Setup autoloading
require 'init_autoloader.php';

// loading development config described in https://www.apigility.org/documentation/recipes/apigility-in-an-existing-zf2-application
if (!defined('APPLICATION_PATH')) {
    define('APPLICATION_PATH', realpath(__DIR__ . '/../'));
}
$appConfig = include APPLICATION_PATH . '/config/application.config.php';
if (file_exists(APPLICATION_PATH . '/config/development.config.php')) {
    $appConfig = Zend\Stdlib\ArrayUtils::merge($appConfig, include APPLICATION_PATH . '/config/development.config.php');
}
// Run the application!
Zend\Mvc\Application::init($appConfig)->run();