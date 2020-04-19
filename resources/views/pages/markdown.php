<!DOCTYPE html>
<html lang="ru">
    <head>
        <title>О проекте</title>
    <app_raw_code>out(@chunks/head.php)</app_raw_code>
</head>
<body>
    <div class="bg-light">
        <div class="container">
            <app_raw_code>out(@chunks/navbar.php)</app_raw_code>
        </div>
    </div>
    <div class="container py-4">
        <div class="row">
            <div class="col-12 col-lg-3 pt-2 pb-4">
                <div class="font-weight-bold pb-2">Содержание</div>
                <nav class="nav flex-column">
                    <a class="nav-link text-info pl-0 pb-0 <app_code>ifequal(active, @chunks/uri_path.php, /about)</app_code>" href="/about">Введение</a>
                    <a class="nav-link text-info pl-0 pb-0 <app_code>ifequal(active, @chunks/uri_path.php, /docs/architecture)</app_code>" href="/docs/architecture">Архитектура приложения</a>
                    <a class="nav-link text-info pl-0 pb-0 <app_code>ifequal(active, @chunks/uri_path.php, /docs/classes)</app_code>" href="/docs/classes">Основные классы</a>
                    <a class="nav-link text-info pl-0 pb-0 <app_code>ifequal(active, @chunks/uri_path.php, /docs/config)</app_code>" href="/docs/config">Конфигурация</a>
                    <a class="nav-link text-info pl-0 pb-0 mt-3 border-top" href="https://api.code-examples.vs24.su" target="_blank"><i class="fas fa-code mr-1"></i> Документация API</a>
                    <a class="nav-link text-info pl-0 pb-0" href="https://github.com/PetrovichMirr/code-examples" target="_blank"><i class="fab fa-github mr-1"></i> Исходный код на GitHub</a>
                </nav>
            </div>
            <div class="col-12 col-lg-9">
                <app_raw_code>out($html)</app_raw_code>
            </div>
        </div>
    </div>
<app_raw_code>out(@chunks/footer.php)</app_raw_code>
<app_raw_code>out(@chunks/js.php)</app_raw_code>
</body>
</html>
