<?php

namespace App\Contracts;

/**
 * Содержит данные обработки маршрута (роута)
 *
 * @author petrovich
 */
interface RouteAction
{

    /**
     * Возвращает экземпляр класса.
     *
     * @param callable $action Обработчик маршрута (роута).
     * @param mixed $params Параметры для обработчика маршрута (роута).
     *
     * @return this Экземпляр класса
     */
    public function __construct($action, $params);

    /**
     * Возвращает обработчик маршрута (роута).
     *
     * @return callable Обработчик маршрута (роута).
     */
    public function getAction();

    /**
     * Возвращает параметры для обработчика маршрута (роута).
     *
     * @return mixed Параметры для обработчика маршрута (роута).
     */
    public function getParams();
}
