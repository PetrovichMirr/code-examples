<?php

namespace App\Contracts;

/**
 * Маршрутизация (роутинг)
 *
 * @author petrovich
 */
interface Route
{

    /**
     * Устанавливает обработчик GET-запроса для указанного шаблона пути.
     *
     * @param string $pattern Шаблон пути
     * @param callable $action Обработчик запроса
     *
     * @return mixed Обработчик запроса для указанного пути.
     */
    public function get($pattern, $action);

    /**
     * Устанавливает обработчик POST-запроса для указанного шаблона пути.
     *
     * @param string $pattern Шаблон пути
     * @param callable $action Обработчик запроса
     *
     * @return mixed Обработчик запроса для указанного пути.
     */
    public function post($pattern, $action);

    /**
     * Возвращает обработчик для указанного пути и метода запроса.
     * Если обработчик не определён, возвращает null.
     *
     * @param string $uriPath Относительный путь URI
     * @param string $method Метод запроса
     *
     * @return \App\Contracts\RouteAction Данные для обработки запроса для указанного пути и метода запроса.
     */
    public function getAction($uriPath, $method);
}
