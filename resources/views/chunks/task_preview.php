<div class="mt-4 pb-2 border-bottom">
    <h3 class="mb-3">
        <a href="tasks/<app_code>out($task->id)</app_code>" class="text-info text-decoration-none">
            Задача № <app_code>out($task->id)</app_code>
        </a>
    </h3>
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
    <app_raw_code>ifout(@chunks/task_admin_actions.php, $user)</app_raw_code>
</div>