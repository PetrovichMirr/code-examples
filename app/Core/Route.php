<?php

namespace App\Core;

use App\Contracts\Route as IRoute;
use App\Contracts\Request;
use App\Core\RouteAction;

/**
 * Маршрутизация (роутинг)
 *
 * @author petrovich
 */
class Route implements IRoute
{

    /**
     * Список маршрутов и обработчиков запроса
     * Формат списка:
     * [
     *    [ 'action' => обработчик_запроса,
     *       'method' => метод_запроса,
     *       'pattern' => шаблон_пути,
     *       'name' => имя_роута(может отсутствовать) ],
     *    ...
     * ]
     *
     * @var array
     */
    private $routes = [];

    /**
     * Возвращает канонический формат указанного пути URI: в нижнем регистре,
     * без начальных и конечных слешей, кроме URI главной страницы ('/').
     *
     * @param string $uriPath Относительный путь URI
     *
     * @return string канонический формат указанного пути URI.
     */
    private function getCanonicalUriPath($uriPath)
    {
        $trimUriPath = strtolower(trim($uriPath, ' /'));
        return $trimUriPath == '' ? '/' : $trimUriPath;
    }

    /**
     * Устанавливает обработчик для указанного шаблона пути.
     * Форматы шаблона пути: 'path', 'path/{}', где:
     * path - любой допустимый uri-путь, например 'qwerty/asdf';
     * {} - динамическая часть шаблона.
     * Например, шаблону пути qwerty/asdf/{}' будут
     * сооветствовать пути 'qwerty/asdf/123', 'qwerty/asdf/zxcv' и т.д.
     * Шаблон пути для корневой (главной) страницы: '/'
     * Динамический шаблон пути для корневой (главной) страницы: '{}'
     *
     * @param string $pattern Шаблон пути
     * @param callable $action Обработчик запроса
     * @param string $method Метод запроса
     * @param string $routeName Имя маршрута (роута)
     *
     * @return mixed Обработчик запроса для указанного пути.
     */
    private function setAction($pattern, $action, $method, $routeName = null)
    {
        // Проверяем возможность вызова обработчика
        if (!is_callable($action)) {
            throw new \ErrorException('Route action is not callable');
        }

        // Придаём шаблону канонический формат
        $canonicalPattern = $this->getCanonicalUriPath($pattern);
        $route = ['pattern' => $canonicalPattern, 'action' => $action, 'method' => $method];
        if (isset($routeName)) {
            $route['name'] = $routeName;
        }
        $this->routes[] = $route;
    }

    /**
     * Устанавливает обработчик GET-запроса для указанного шаблона пути.
     *
     * @param string $pattern Шаблон пути
     * @param callable $action Обработчик запроса
     *
     * @return mixed Обработчик запроса для указанного пути.
     */
    public function get($pattern, $action)
    {
        $this->setAction($pattern, $action, Request::REQUEST_METHOD_GET);
    }

    /**
     * Устанавливает обработчик POST-запроса для указанного шаблона пути.
     *
     * @param string $pattern Шаблон пути
     * @param callable $action Обработчик запроса
     *
     * @return mixed Обработчик запроса для указанного пути.
     */
    public function post($pattern, $action)
    {
        $this->setAction($pattern, $action, Request::REQUEST_METHOD_POST);
    }

    /**
     * Возвращает обработчик для указанных шаблона пути и метода запроса.
     * Если обработчик не определён, возвращает null.
     *
     * @param string $pattern Шаблон пути
     * @param string $method Метод запроса
     *
     * @return mixed Обработчик запроса для указанных шаблона пути и метода запроса.
     */
    private function searchAction($pattern, $method)
    {
        foreach ($this->routes as $routeData) {
            if (($routeData['pattern'] == $pattern) &&
                    ($routeData['method'] == $method)) {
                return $routeData['action'];
            }
        }
        // Если обработчик не определён, возвращаем null.
        return null;
    }

    /**
     * Возвращает данные для обработки запроса.
     *
     * @param callable $action Обработчик маршрута (роута).
     * @param mixed $params Параметры для обработчика маршрута (роута).
     *
     * @return \App\Contracts\RouteAction Данные для обработки запроса.
     */
    private function makeRouteAction($action, $params = null)
    {
        return new RouteAction($action, $params);
    }

    /**
     * Возвращает данные для обработчика для указанных пути и метода запроса.
     * Если обработчик не определён, возвращает null.
     *
     * @param string $uriPath Относительный путь URI
     * @param string $method Метод запроса
     *
     * @return \App\Contracts\RouteAction Данные для обработки запроса для указанных пути и метода запроса.
     */
    public function getAction($uriPath, $method)
    {
        // Придаём пути канонический формат
        $canonicalUriPath = $this->getCanonicalUriPath($uriPath);

        // Ищем соответствия шаблону 'path'
        $searchAction = $this->searchAction($canonicalUriPath, $method);
        if (isset($searchAction)) {
            return $this->makeRouteAction($searchAction);
        }
        // Если по шаблону 'path' обработчик не найден, ищем соответствия шаблону 'path/{}'
        // Добавляем начальный слеш, чтобы включить в соответствия и главную страницу с шаблоном '{}'
        $dynamicUriPath = '/' . $canonicalUriPath;
        $patternPathLength = strrpos($dynamicUriPath, '/');
        if ($patternPathLength !== false) {
            $pattern = substr($dynamicUriPath, 1, $patternPathLength) . '{}';
            $searchAction = $this->searchAction($pattern, $method);
            if (isset($searchAction)) {
                // Динамические параметры маршрута (роута)
                $params = substr($dynamicUriPath, $patternPathLength + 1);
                return $this->makeRouteAction($searchAction, $params);
            }
        }

        // Если обработчик не определён, возвращаем null.
        return null;
    }

    /**
     * Выполняет поиск в массиве маршрутов (роутов) по заданному ключу и значению
     * Если маршрут (роут) не найден, возвращает null.
     *
     * @param string $key Ключ для поиска ('pattern', action', 'method', 'name')
     * @param string $value Значение для поиска.
     *
     * @return array|null Найденный роут или null.
     */
    public function findRoute($key, $value)
    {
        foreach ($this->routes as $routeData) {
            if ($routeData[$key] == $value) {
                return $routeData;
            }
        }
        return null;
    }

    /**
     * Возвращает путь URI для именованного маршрута (роута).
     * Если маршрут (роут) с указанным именем не найден, возвращает null.
     *
     * @param string $routeName Имя маршрута (роута).
     * @param mixed $params Параметры для обработчика маршрута (роута).
     *
     * @return string|null Путь URI для именованного маршрута (роута).
     */
    public function makeUriPath($routeName, $params)
    {
        $foundRouteData = $this->findRoute('name', $routeName);
        if (!isset($foundRouteData)) {
            return null;
        }
        return str_replace('{}', $params, $foundRouteData['pattern']);
    }

}
