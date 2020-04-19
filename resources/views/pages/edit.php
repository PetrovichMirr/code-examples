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
        <h1>Редактирование задачи № <app_code>out($task->id)</app_code></h1>
        <div>
            <form class="mt-4" action="/tasks/edit/<app_code>out($task->id)</app_code>" method="post">
                <div class="form-group">
                    <label for="user_name">Имя</label>
                    <input type="text" class="form-control <app_code>ifout(is-invalid, $errors[user_name])</app_code>" id="user_name" name="user_name" placeholder="Введите своё имя" value="<app_code>out($task->user_name)</app_code>">
                    <div class="invalid-feedback">
                        <app_code>out($errors[user_name])</app_code>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="text" class="form-control <app_code>ifout(is-invalid, $errors[email])</app_code>" id="email" name="email" placeholder="Введите e-mail" value="<app_code>out($task->email)</app_code>">
                    <div class="invalid-feedback">
                        <app_code>out($errors[email])</app_code>
                    </div>
                </div>
                <div class="form-group">
                    <label for="content">Текст задачи</label>
                    <textarea class="form-control <app_code>ifout(is-invalid, $errors[content])</app_code>" id="content" name="content" rows="5" placeholder="Введите текст задачи"><app_code>out($task->content)</app_code></textarea>
                    <div class="invalid-feedback">
                        <app_code>out($errors[content])</app_code>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" <app_code>ifout(checked, $task->done)</app_code> id="done" name="done">
                        <label class="form-check-label" for="done">
                            Выполнена
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-outline-info">Сохранить</button>
            </form>
            <p class="text-muted mt-4 mb-2">
                <span class="mr-2">Добавил: <i class="fas fa-child mr-1"></i> <app_code>out($task->user_name)</app_code></span>
                <span>|</span>
                <span class="mx-2"><i class="far fa-envelope mr-1"></i> <app_code>out($task->email)</app_code></span>
                <span>|</span>
                <span class="mx-2">Статус: <app_code>@phpeval(echo $viewData['task']->done ? 'выполнена' : 'не выполнена';)</app_code></span>
                <span>|</span>
                <span class="mx-2">Добавлена: <i class="far fa-clock mr-1"></i> <app_code>out($task->created_at)</app_code></span>
                <span>|</span>
                <span class="mx-2">Обновлена: <i class="far fa-clock mr-1"></i> <app_code>out($task->updated_at)</app_code></span>
            </p>
            <app_raw_code>ifout(@chunks/task_edited_by_admin.php, $task->edited_by_admin)</app_raw_code>
        </div>
    </div>
<app_raw_code>out(@chunks/footer.php)</app_raw_code>
<app_raw_code>out(@chunks/js.php)</app_raw_code>
</body>
</html>
