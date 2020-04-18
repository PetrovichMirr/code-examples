<?php

namespace App\Contracts;

/**
 * Ответ приложения на HTTP-запрос.
 *
 * @author petrovich
 */
interface Response
{

    // Коды ошибок HTTP
    const HTTP_ERRORS = [
        '404' => [
            'code' => '404 Not Found',
            'description' => 'Запрашиваемая страница не найдена.',
        ],
    ];

    /**
     * Устанавливает значение тела ответа.
     *
     * @param string $value Значение тела ответа.
     *
     * @return void
     */
    public function setBody($value);

    /**
     * Получает значение тела ответа.
     *
     * @return string Тело ответа.
     */
    public function getBody();

    /**
     * Отправляет ответ приложения.
     *
     * @return void
     */
    public function send();
}
