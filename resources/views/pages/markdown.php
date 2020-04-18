<!DOCTYPE html>
<html lang="ru">
    <head>
        <title>О проекте</title>
    <app_raw_code>out(@chunks/head.php)</app_raw_code>
</head>
<body>
    <div class="bg-light">
        <div class="container">
            <app_raw_code>out(@chunks/navbar_no_active.php)</app_raw_code>
        </div>
    </div>
    <div class="container py-4">
        <div class="row">
            <div class="col-12 col-lg-3 pt-2 pb-4">
                <div class="font-weight-bold pb-2">Содержание</div>
                <nav class="nav flex-column">
                    <a class="nav-link text-info pl-0 pb-0" href="/about">Введение</a>
                    <a class="nav-link text-info pl-0 pb-0" href="/docs/architecture">Архитектура</a>
                    <a class="nav-link text-info pl-0 pb-0" href="https://api-code-examples.vs24.su" target="_blank">Документация API</a>
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
