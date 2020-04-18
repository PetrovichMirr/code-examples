<div class="mb-2">
    <a class="btn btn-outline-info d-inline-block mr-3" role="button" href="tasks/edit/<app_code>out($task->id)</app_code>">
        <i class="far fa-edit mr-1"></i> Редактировать
    </a>
    <form class="d-inline-block" action="/tasks/destroy/<app_code>out($task->id)</app_code>" method="post">
        <button type="submit" class="btn btn-outline-danger">
            <i class="far fa-trash-alt mr-1"></i>  Удалить
        </button>
    </form>
</div>