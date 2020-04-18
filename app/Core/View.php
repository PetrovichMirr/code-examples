<?php

namespace App\Core;

use App\Contracts\View as IView;
use App\Core\ViewTemplate\Render;

/**
 * Класс вида
 *
 * @author petrovich
 */
class View implements IView
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
    public function render($viewFile, $data = [])
    {
        $render = new Render();
        return $render->handle(file_get_contents(APP_PATH_DIR_VIEWS . '/' . $viewFile), $data);
    }

}
