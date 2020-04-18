Архитектура приложения
========================

>Это пример цитаты,
>в которой перед каждой строкой
>ставится угловая скобка.

Пример кода

    // Setup a new Eloquent Capsule instance
    $capsule = new Capsule;
    $capsule->addConnection([
        'driver' => APP_DB_DRIVER,
        'host' => APP_DB_HOST,
        'database' => getenv('APP_DB_NAME'),
        'username' => getenv('APP_DB_USER'),
        'password' => getenv('APP_DB_PASSWORD'),
        'charset' => APP_DB_CHARSET,
        'collation' => APP_DB_CHARSET_COLLATION,
    ]);
    $capsule->bootEloquent();

Далее - обычный текст.

Текст, выделенный курсивом с использованием синтаксиса языка Markdown, выглядит следующим образом:

*Пример*  

Текст, выделенный полужирным шрифтом с использованием синтаксиса языка Markdown, выглядит следующим образом:

**Пример**

Текст, выделенный курсивным полужирным шрифтом с использованием синтаксиса языка Markdown выглядит следующим образом:

***Пример***

Пример кода 2: `$data = view()->render('pages/login.php', [
    'error' => '',
    'scriptTime' => (microtime(true) - APP_START),
]);` Далее - обычный текст.

Пример кода 2:

`$data = view()->render('pages/login.php', [
    'error' => '',
    'scriptTime' => (microtime(true) - APP_START),
]);`

Далее - обычный текст.