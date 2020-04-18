<?php

namespace App\Contracts;

/**
 * Обработка входящего HTTP-запроса
 *
 * @author petrovich
 */
interface Request
{

    const REQUEST_METHOD_GET = 'GET'; // Метод HTTP-запроса: GET.
    const REQUEST_METHOD_POST = 'POST'; // Метод HTTP-запроса: POST.
    const REQUEST_METHOD_OTHER = 'OTHER'; // Метод HTTP-запроса: иные типы.

    /**
     * Обрабатывает входящий HTTP-запрос
     *
     * @return void
     */
    public function handle();

    /**
     * Возвращает относительный URI - путь без строки запроса
     *
     * @return string
     */
    public function getUriPath();

    /**
     * Возвращает метод HTTP-запроса (GET / POST).
     *
     * @return string
     */
    public function getMethod();
}
