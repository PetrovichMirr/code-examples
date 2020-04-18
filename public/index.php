<?php

/**
 * Пример кода: список задач
 *
 * @author   Минеев Сергей <vseti-24@mail.ru>
 */
//
// Время запуска скрипта
define('APP_START', microtime(true));

// Корневая директория приложения
define('APP_PATH_DIR_ROOT', rtrim(dirname(__DIR__), ' /'));

// Директория с основными файлами приложения
define('APP_PATH_DIR_APP', APP_PATH_DIR_ROOT . '/app');

// Директория с файлами ядра
define('APP_PATH_DIR_CORE', APP_PATH_DIR_APP . '/Core');

// Директория с файлами composer
define('APP_PATH_DIR_VENDOR', APP_PATH_DIR_ROOT . '/vendor');

// Путь к файлу конфигурации приложения
define('APP_PATH_FILE_CONFIG', APP_PATH_DIR_APP . '/config.php');

// Путь к файлу вспомогательных функций (хелперов)
define('APP_PATH_FILE_HELPERS', APP_PATH_DIR_CORE . '/helpers.php');

// Загрузка конфигурации приложения
require APP_PATH_FILE_CONFIG;

// Режим отладки
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', '1');
}

// Подключение автозагрузчика composer
require APP_PATH_DIR_VENDOR . '/autoload.php';

// Подключение вспомогательных функций (хелперов)
require APP_PATH_FILE_HELPERS;

// Подключение phpdotenv для работы с переменными среды из файла .env
$dotenv = \Dotenv\Dotenv::createImmutable(APP_PATH_DIR_ROOT); 
$dotenv->load();

// Запуск приложения
kernel()->handle();
