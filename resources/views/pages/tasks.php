<!DOCTYPE html>
<html lang="ru">
    <head>
        <title>Список задач</title>
    <app_raw_code>out(@chunks/head.php)</app_raw_code>
</head>
<body>
    <div class="bg-light">
        <div class="container">
            <app_raw_code>out(@chunks/navbar.php)</app_raw_code>
        </div>
    </div>
    <div class="container py-4">
        <h1>Список задач</h1>
        <app_raw_code>ifout(@chunks/task_added.php, $added)</app_raw_code>
        <div>
            <div class="mt-3">
                <p class="text-muted mb-2">
                    <span class="font-weight-bold mb-1 mx-1">Сортировка</span>
                    <app_raw_code>loopforeach($orderAttrs, name, desc, @chunks/sort_item.php)</app_raw_code>
                </p>
            </div>
            <div>
                <app_raw_code>loopforeach($tasks, key, task, @chunks/task_preview.php)</app_raw_code>
            </div>
            <div class="mt-4">
                <app_raw_code>out(@chunks/pagination.php)</app_raw_code>
            </div>
            <h2 class="mt-5">Добавить новую задачу</h2>
            <form class="mt-4" action="/" method="post">
                <div class="form-group">
                    <label for="user_name">Имя</label>
                    <input type="text" class="form-control <app_code>ifout(is-invalid, $errors[user_name])</app_code>" id="user_name" name="user_name" placeholder="Введите своё имя" value="<app_code>out($oldInputs[user_name])</app_code>">
                    <div class="invalid-feedback">
                        <app_code>out($errors[user_name])</app_code>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="text" class="form-control <app_code>ifout(is-invalid, $errors[email])</app_code>" id="email" name="email" placeholder="Введите e-mail" value="<app_code>out($oldInputs[email])</app_code>">
                    <div class="invalid-feedback">
                        <app_code>out($errors[email])</app_code>
                    </div>
                </div>
                <div class="form-group">
                    <label for="content">Текст задачи</label>
                    <textarea class="form-control <app_code>ifout(is-invalid, $errors[content])</app_code>" id="content" name="content" rows="5" placeholder="Введите текст задачи"><app_code>out($oldInputs[content])</app_code></textarea>
                    <div class="invalid-feedback">
                        <app_code>out($errors[content])</app_code>
                    </div>
                </div>
                <button type="submit" class="btn btn-outline-info">Добавить</button>
            </form>
        </div>
    </div>
<app_raw_code>out(@chunks/footer.php)</app_raw_code>
<app_raw_code>out(@chunks/js.php)</app_raw_code>
</body>
</html>
