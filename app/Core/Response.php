<?php

namespace App\Core;

use App\Contracts\Response as IResponse;

/**
 * Ответ приложения на HTTP-запрос.
 *
 * @author petrovich
 */
class Response implements IResponse
{

    /**
     * Тело ответа.
     *
     * @var string
     */
    private $body;

    /**
     * Устанавливает значение тела ответа.
     *
     * @param string $value Значение тела ответа.
     *
     * @return void
     */
    public function setBody($value)
    {
        $this->body = $value;
        return $this;
    }

    /**
     * Получает значение тела ответа.
     *
     * @return string Тело ответа.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Отправляет ответ приложения.
     *
     * @param int $code Код состояния HTTP.
     *
     * @return void
     */
    public function send($code = '200 OK')
    {
        header('HTTP/1.0 ' . $code);
        echo $this->body;
    }

    /**
     * Возвращает ответ с заданным HTTP-кодом ошибки (например, 404)
     * и завершает приложение.
     *
     * @param int $code Код состояния HTTP.
     *
     * @return void
     */
    public function error($code)
    {
        $httpErrors = self::HTTP_ERRORS;
        if (!isset($httpErrors[$code])) {
            throw new \ErrorException("HTTP code {$code} not defined");
        }
        $httpCode = $httpErrors[$code]['code'];
        $description = $httpErrors[$code]['description'];

        if (defined('APP_PATH_FILE_VIEW_ERROR') && !empty(APP_PATH_FILE_VIEW_ERROR)) {
            $body = view()->render('pages/error.php', [
                'code' => $httpCode,
                'description' => $description,
            ]);
        } else {
            $body = "{$httpCode}. {$description}";
        }
        response()->setBody($body)->send($httpCode);
        exit;
    }

    /**
     * Редирект
     *
     * @param string $url URL редиректа.
     *
     * @return void
     */
    public function redirect($url)
    {
        header("Location: {$url}");
        exit;
    }

}
