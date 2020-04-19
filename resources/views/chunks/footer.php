<div class="bg-dark text-white p-3">
    <p class="mb-2">Время работы скрипта: <app_code>@phpeval(echo number_format((microtime(true) - APP_START), 6, '.', ' ');)</app_code> секунд.</p>
    <p class="mb-2">Пиковое значение выделенного объема памяти: <app_code>@phpeval(return number_format(memory_get_peak_usage(true), 0, '.', ' ');)</app_code> байт.</p>
    <p class="mb-0">&copy; 2020 PHP-КОДОВСТВО.</p>
</div>