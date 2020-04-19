<!DOCTYPE html>
<html lang="ru">
    <head>
        <title>Ошибка</title>
    <app_raw_code>out(@chunks/head.php)</app_raw_code>
</head>
<body>
    <div class="bg-light">
        <div class="container">
            <app_raw_code>out(@chunks/navbar.php)</app_raw_code>
        </div>
    </div>
    <div class="container py-4">
        <h1><i class="far fa-meh-rolling-eyes"></i> Что-то пошло не так!</h1>
        <div>
            <p>Ошибка <app_code>out($code)</app_code>. <app_code>out($description)</app_code></p>
            <p><a href="/" class="text-info">Вернуться на главную страницу</a></p>
        </div>
    </div>
<app_raw_code>out(@chunks/footer.php)</app_raw_code>
<app_raw_code>out(@chunks/js.php)</app_raw_code>
</body>
</html>
