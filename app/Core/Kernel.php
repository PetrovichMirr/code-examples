<?php


namespace App\Core;

use App\Contracts\Kernel as IKernel;

/**
 * Ядро приложения.
 *
 * @author petrovich
 */
class Kernel implements IKernel
{

    /**
     * Выполнение приложения: обработка входящего HTTP-запроса и возвращение ответа
     *
     * @return void
     */
    public function handle()
    {
        // Обрабатываем входящий HTTP-запрос
        $request = request();
        $request->handle();

        // Загружаем маршруты (роуты)
        require APP_PATH_FILE_ROUTES;

        // Определяем обработчик запроса
        $routeAction = route()->getAction($request->getUriPath(), $request->getMethod());

        // Проверяем наличие обработчика
        if (!isset($routeAction)) {
            response()->error(404);
        }

        // Вызываем обработчик и отправляем ответ приложения
        // Обработчик должен возвращать ответ (\App\Contracts\Response) или данные для тела ответа
        $response = call_user_func($routeAction->getAction(), $routeAction->getParams());
        if ($response instanceof \App\Contracts\Response) {
            $response->send();
        } else {
            response()->setBody($response)->send();
        }
        // Режим отладки
        if (APP_DEBUG) {
            echo 'Полное время работы скрипта: ', (microtime(true) - APP_START), ' секунд.';
        }
        exit;
    }

}
