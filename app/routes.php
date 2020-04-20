<?php

/**
 * Маршруты (роуты) приложения
 *
 */
//
//
//
// Привязка действия к роуту
//
// 1. Анонимная функция
//route()->get('/', function () {
//    return '<br>*** HOME! *** 1. Анонимная функция <br>';
//});
//
// То же, с передачей динамических параметров маршрута
//route()->get('dinamic/{}', function ($params) {
//    return '<br>*** HOME! *** 1. Анонимная функция.<br>Параметр: ' . $params;
//});
//
//
//
// 2. Имя функции
//function app_route_action()
//{
//    return '<br>*** HOME! *** 2. Имя функции<br>';
//}
//route()->get('/', 'app_route_action');
//
// То же, с передачей динамических параметров маршрута
//function app_route_action_dinamic($params)
//{
//    return '<br>*** HOME! *** 2. Имя функции.<br>Параметр: ' . $params;
//}
//route()->get('dinamic/{}', 'app_route_action_dinamic');
//
//
//
// 3. Метод класса, например метод контроллера.
// Для передачи динамических параметров маршрута
// укажите параметр в методе контроллера, например:
// public function index($params) ...
//
//
//
// Динамический шаблон для страницы
//route()->get('test/{}', function () {
//    return '<br>*** ACTION! *** <br>';
//});
// Динамический шаблон для главной страницы
//route()->get('{}', function () {
//    return '<br>*** HOME dinamic! *** <br>';
//});
//
//
//
// Маршруты приложения
route()->get('/', [
    new \App\Controllers\TaskController(),
    'index',
]);
route()->post('/', [
    new \App\Controllers\TaskController(),
    'store',
]);
route()->get('tasks/{}', [
    new \App\Controllers\TaskController(),
    'show',
]);
route()->get('tasks/edit/{}', [
    new \App\Controllers\TaskController(),
    'edit',
]);
route()->post('tasks/edit/{}', [
    new \App\Controllers\TaskController(),
    'update',
]);
route()->post('tasks/destroy/{}', [
    new \App\Controllers\TaskController(),
    'destroy',
]);
route()->get('login', [
    new \App\Controllers\TaskController(),
    'loginIndex',
]);
route()->post('login', [
    new \App\Controllers\TaskController(),
    'login',
]);
route()->get('logout', [
    new \App\Controllers\TaskController(),
    'logout',
]);
route()->get('about', [
    new \App\Controllers\TaskController(),
    'aboutIndex',
]);
route()->get('docs/{}', [
    new \App\Controllers\TaskController(),
    'docsShow',
]);
route()->get('contacts', [
    new \App\Controllers\TaskController(),
    'contactsIndex',
]);
// Страница с выполнением тестового задания
route()->get('test', [
    new \App\Controllers\TestController(),
    'testIndex',
]);
