<?php

namespace App\Core;

use App\Contracts\Request as IRequest;

/**
 * Обработка входящего HTTP-запроса
 *
 * @author petrovich
 */
class Request implements IRequest
{

    /**
     * Относительный URI - путь без строки запроса
     *
     * @var array
     */
    private $uriPath;

    /**
     * Метод HTTP-запроса (GET / POST).
     *
     * @var array
     */
    private $method;

    /**
     * Список параметров GET-запроса.
     *
     * @var array
     */
    private $get = [];

    /**
     * Список параметров POST-запроса.
     *
     * @var array
     */
    private $post = [];

    /**
     * Возвращает массив, представляющий собой слитое
     * содержимое GET или POST массивов.
     *
     * @return array Возвращает массив, представляющий собой слитое содержимое GET или POST массивов.
     */
    public function inputs()
    {
        return array_merge($this->get, $this->post);
    }

    /**
     * Возвращает значение GET или POST параметра входящего запроса.
     * Если параметр с заданным именем отсутствует, возвращает
     * значение по умолчанию $default
     *
     * @param string $name Имя параметра входящего запроса
     * @param string $default Значение по умолчанию
     *
     * @return string|null Значение GET или POST заданного параметра входящего запроса или null, если параметр отсутствует
     */
    public function input($name, $default = null)
    {
        $inputArr = array_merge($this->get, $this->post);
        return isset($inputArr[$name]) ? $inputArr[$name] : $default;
    }

    /**
     * Определяет, существует ли значение во входящем запросе.
     *
     * @param string $name Имя параметра входящего запроса
     *
     * @return bool Возвращает true, если значение существует во входящем запросе.
     */
    public function hasInput($name)
    {
        $inputArr = array_merge($this->get, $this->post);
        return isset($inputArr[$name]);
    }

    /**
     * Определяет метод входящего HTTP-запроса
     *
     * @return void
     */
    private function setMethod()
    {
        $filterInput = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        switch ($filterInput) {
            case 'GET':
                $this->method = IRequest::REQUEST_METHOD_GET;
                break;
            case 'POST':
                $this->method = IRequest::REQUEST_METHOD_POST;
                break;
            default:
                $this->method = IRequest::REQUEST_METHOD_OTHER;
        }
    }

    /**
     * Извлекает параметры запроса
     *
     * @return void
     */
    private function setRequestParams()
    {
        // Определяем метод запроса
        $this->setMethod();
        $this->uriPath = parse_url(filter_input(INPUT_SERVER, 'REQUEST_URI'), PHP_URL_PATH);
        $filterGet = filter_input_array(INPUT_GET);
        $filterPost = filter_input_array(INPUT_POST);
        $this->get = empty($filterGet) ? [] : $filterGet;
        $this->post = empty($filterPost) ? [] : $filterPost;
    }

    /**
     * Обрабатывает входящий HTTP-запрос
     *
     * @return void
     */
    public function handle()
    {
        // Получаем параметры запроса
        $this->setRequestParams();
    }

    /**
     * Возвращает относительный URI - путь без строки запроса
     *
     * @return string
     */
    public function getUriPath()
    {
        return $this->uriPath;
    }

    /**
     * Возвращает метод HTTP-запроса (GET / POST).
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

}
