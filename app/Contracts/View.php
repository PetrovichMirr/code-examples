<?php

namespace App\Contracts;

/**
 * Класс вида
 *
 * @author petrovich
 */
interface View
{

    /**
     * Осуществляет ренедеринг вида (выводит данные в шаблон вида) и возвращает результат.
     * Формат данных для вывода: [ имя_переменной_шаблона => значение_переменной_шаблона]
     *
     * @param string $viewFile Путь к файлу вида (относительно директории с файлами видов)
     * @param array $data Данные для вывода.
     *
     * @return string Результат рендеринга вида
     */
    public function render($viewFile, $data = []);
}
