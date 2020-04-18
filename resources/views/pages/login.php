<!DOCTYPE html>
<html lang="ru">
    <head>
        <title>Вход</title>
    <app_raw_code>out(@chunks/head.php)</app_raw_code>
</head>
<body>
    <div class="bg-light">
        <div class="container">
            <app_raw_code>out(@chunks/navbar_no_active.php)</app_raw_code>
        </div>
    </div>
    <div class="container py-4">
        <h1>Вход</h1>
        <div>
            <app_raw_code>ifout(@chunks/login_error.php, $error)</app_raw_code>
            <form action="/login" method="post">
                <div class="form-group">
                    <label for="login">Имя</label>
                    <input type="text" class="form-control" id="login" name="login" placeholder="Логин">
                </div>
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Пароль">
                </div>
                <button type="submit" class="btn btn-outline-info">Войти</button>
            </form>
        </div>
    </div>
<app_raw_code>out(@chunks/footer.php)</app_raw_code>
<app_raw_code>out(@chunks/js.php)</app_raw_code>
</body>
</html>
