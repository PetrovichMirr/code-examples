<li class="page-item <app_code>@phpeval(echo $viewData['pagination']['current_page'] == $viewData['iteration'] ? 'active' : '';)</app_code>">
    <a class="page-link" href="<app_code>out($uriPath)</app_code>?page_num=<app_code>out($iteration)</app_code>&<app_code>out($orderKey)</app_code>=<app_code>out($orderValue)</app_code>">
        <app_code>out($iteration)</app_code>
    </a>
</li>