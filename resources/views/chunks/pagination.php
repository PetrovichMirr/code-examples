<nav>
    <ul class="pagination">
        <li class="page-item">
            <a class="page-link" href="<app_code>@phpeval(return request()->getUriPath();)</app_code>?page_num=<app_code>@phpeval(echo $viewData['pagination']['current_page'] > 1 ? $viewData['pagination']['current_page'] - 1 : 1;)</app_code>">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <app_raw_code>loopfor(1, $pagination[pages_count], 1, @chunks/pagination_item.php)</app_raw_code>
        <li class="page-item">
            <a class="page-link" href="<app_code>@phpeval(return request()->getUriPath();)</app_code>?page_num=<app_code>@phpeval(echo $viewData['pagination']['current_page'] < $viewData['pagination']['pages_count'] ? $viewData['pagination']['current_page'] + 1 : $viewData['pagination']['pages_count'];)</app_code>">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>
