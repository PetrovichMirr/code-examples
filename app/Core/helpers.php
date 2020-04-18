<?php

use App\Core\Factory;

if (!function_exists('kernel')) {

    /**
     * Возвращает экземпляр класса приложения (kernel)
     *
     * @return mixed
     */
    function kernel()
    {
        return Factory::make(\App\Contracts\Kernel::class);
    }

}

if (!function_exists('request')) {

    /**
     * Возвращает экземпляр класса запроса (request)
     *
     * @return mixed
     */
    function request()
    {
        return Factory::make(\App\Contracts\Request::class);
    }

}

if (!function_exists('route')) {

    /**
     * Возвращает экземпляр класса маршрутизации (route)
     *
     * @return mixed
     */
    function route()
    {
        return Factory::make(\App\Contracts\Route::class);
    }

}

if (!function_exists('response')) {

    /**
     * Возвращает экземпляр класса ответа на запрос (response)
     *
     * @return mixed
     */
    function response()
    {
        return Factory::make(\App\Contracts\Response::class);
    }

}

if (!function_exists('view')) {

    /**
     * Возвращает экземпляр класса вида (view)
     *
     * @return mixed
     */
    function view()
    {
        return Factory::make(\App\Contracts\View::class);
    }

}

if (!function_exists('attributeSettings')) {

    /**
     * Возвращает экземпляр класса вида (attributeSettings)
     *
     * @return mixed
     */
    function attributeSettings()
    {
        return Factory::make(\App\Contracts\AttributeSettings::class);
    }

}

if (!function_exists('inputChain')) {

    /**
     * Возвращает экземпляр класса вида (inputChain)
     *
     * @return mixed
     */
    function inputChain()
    {
        return Factory::make(\App\Contracts\InputChain::class);
    }

}

if (!function_exists('pagination')) {

    /**
     * Возвращает данные для постраничной навигации в виде массива.
     * Формат возвращаемого массива:
     * [ 'pages_count' => количество_страниц, 'offset' => смещение_вывода_элементов, 'current_page' => номер_текущей_страницы ]
     * Комментарий к параметру 'offset' => смещение_вывода_элементов:
     * Параметр offset определяет индекс первого элемента на заданной странице.
     * Отсчёт элементов начинается с 0,
     * например при offset = 1 элементы будут выводиться,
     * начиная со второго элемента общего списка.
     *
     * Комментарий к параметру 'current_page' => номер_текущей_страницы:
     * Функция проверяет корректность заданного номера страницы по условию
     * 1 <= $currentPage <= $pagesCount.
     * Если заданный номер страницы выходит за рамки
     * этого диапазона, он будет изменен:
     * если $currentPage < 1, будет установлено значение 1,
     * если $currentPage > $pagesCount, будет установлено значение $pagesCount.
     *
     * Если количество элементов = 0, возвращает количество страниц = 1.
     *
     * @param int $itemsCount Общее количество элементов
     * @param int $perPage Количество элементов на странице
     * @param int $currentPage Номер текущей страницы
     *
     * @return array
     */
    function pagination($itemsCount, $perPage, $currentPage)
    {
        $pagesCount = (($itemsCount % $perPage) == 0) ? $itemsCount / $perPage : ceil($itemsCount / $perPage);
        $pagesCount = 1 <= $pagesCount ? $pagesCount : 1;
        // Проверка 1 <= $currentPage <= $pagesCount
        $currentPage = 1 <= $currentPage ? $currentPage : 1;
        $currentPage = $currentPage <= $pagesCount ? $currentPage : $pagesCount;
        return [
            'pages_count' => $pagesCount,
            'offset' => ($currentPage - 1) * $perPage,
            'current_page' => $currentPage,
        ];
    }

    if (!function_exists('currentPage')) {

        /**
         * Возвращает номер текущей страницы.
         * Номер текущей страницы извлекается из
         * входящего запроса по имени параметра,
         * заданного константой PAGE_QUERY_KEY.
         * Если номер страницы не задан или не корректен, возвращается 1.
         * @see PAGE_QUERY_KEY
         *
         * @return mixed
         */
        function currentPage()
        {
            // Цепочка обработки входных данных
            $inputChain = inputChain()
                    ->setChain([\App\Contracts\InputChain::FILTER_TRIM,
                        \App\Contracts\InputChain::RULE_NOT_EMPTY,
                        \App\Contracts\InputChain::RULE_INT])
                    ->setData(request()->input(PAGE_QUERY_KEY));
            // Проверка корректности ID
            if ($inputChain->handle() !== true) {
                return 1;
            }
            return $inputChain->getData();
        }

    }
}

