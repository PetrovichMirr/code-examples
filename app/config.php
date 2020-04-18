<?php

/**
 * Конфигурация приложения
 *
 */

/**
 * @var bool Режим отладки
 */
const APP_DEBUG = false;
//
// Список основных интерфейсов приложения и параметры их реализации
const APP_CORE_ALIASES = [
    \App\Contracts\Kernel::class => [
        'implements' => \App\Core\Kernel::class,
        'singleton' => true,
    ],
    \App\Contracts\Request::class => [
        'implements' => \App\Core\Request::class,
        'singleton' => true,
    ],
    \App\Contracts\Route::class => [
        'implements' => \App\Core\Route::class,
        'singleton' => true,
    ],
    \App\Contracts\Response::class => [
        'implements' => \App\Core\Response::class,
        'singleton' => true,
    ],
    \App\Contracts\View::class => [
        'implements' => \App\Core\View::class,
        'singleton' => false,
    ],
    \App\Contracts\AttributeSettings::class => [
        'implements' => \App\Core\AttributeSettings::class,
        'singleton' => false,
    ],
    \App\Contracts\InputChain::class => [
        'implements' => \App\Core\InputChain::class,
        'singleton' => false,
    ],
];
//
// Путь к файлу маршрутов (роутов) относительно корневой директории приложения
const APP_PATH_FILE_ROUTES = APP_PATH_DIR_APP . '/routes.php';
//
// Путь к директории с файлами ресурсов (относительно корневой директории приложения)
const APP_PATH_DIR_RESOURCES = APP_PATH_DIR_ROOT . '/resources';
//
// Путь к директории с файлами видов (относительно корневой директории приложения)
const APP_PATH_DIR_VIEWS = APP_PATH_DIR_RESOURCES . '/views';
//
//
// Доступ к БД
//
// Драйвер БД:
const APP_DB_DRIVER = 'mysql';
//
// Хост БД:
const APP_DB_HOST = 'localhost';
//
// Кодировка
const APP_DB_CHARSET = 'utf8';
//
// Кодировка (сопоставление)
const APP_DB_CHARSET_COLLATION = 'utf8_unicode_ci';
//
//
// Количество моделей на странице по умолчанию при использовании постраничной навигации
const MODELS_PER_PAGE = 3;
//
// Имя (ключ) входного параметра (обычно, GET-параметра) при использовании постраничной навигации
const PAGE_QUERY_KEY = 'page_num';
//
// Путь к файлу вида для вывода ошибок (относительно директории views)
const APP_PATH_FILE_VIEW_ERROR = 'pages/error.php';
