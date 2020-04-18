<?php

namespace App\Core;

use App\Contracts\RouteAction as IRouteAction;

/**
 * Содержит данные обработки маршрута (роута)
 *
 * @author petrovich
 */
class RouteAction implements IRouteAction
{

    /**
     * Обработчик маршрута (роута).
     *
     * @var callable
     */
    private $action;

    /**
     * Параметры для обработчика маршрута (роута).
     *
     * @var mixed
     */
    private $params;

    /**
     * Возвращает экземпляр класса.
     *
     * @param callable $action Обработчик маршрута (роута).
     * @param mixed $params Параметры для обработчика маршрута (роута).
     *
     * @return this Экземпляр класса
     */
    public function __construct($action, $params)
    {
        $this->action = $action;
        $this->params = $params;
    }

    /**
     * Возвращает обработчик маршрута (роута).
     *
     * @return callable Обработчик маршрута (роута).
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Возвращает параметры для обработчика маршрута (роута).
     *
     * @return mixed Параметры для обработчика маршрута (роута).
     */
    public function getParams()
    {
        return $this->params;
    }

}
