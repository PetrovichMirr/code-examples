<?php

namespace App\Contracts;

/**
 * Ядро приложения.
 *
 * @author petrovich
 */
interface Kernel
{

    /**
     * Выполнение приложения: обработка входящего HTTP-запроса и возвращение ответа
     *
     * @return void
     */
    public function handle();
}
