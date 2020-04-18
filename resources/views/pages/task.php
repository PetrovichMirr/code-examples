<!DOCTYPE html>
<html lang="ru">
    <head>
        <title>Список задач</title>
    <app_raw_code>out(@chunks/head.php)</app_raw_code>
</head>
<body>
    <div class="bg-light">
        <div class="container">
            <app_raw_code>out(@chunks/navbar_no_active.php)</app_raw_code>
        </div>
    </div>
    <div class="container py-4">
        <h1>Задача № <app_code>out($task->id)</app_code></h1>
        <div>
            <p>
                <span><app_code>out($task->content)</app_code></span>
            </p>
            <p class="text-muted mb-2">
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
