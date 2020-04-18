<?php

namespace App\Core\ViewTemplate;

/**
 * Выполняет команды в динамических блоках шаблона.
 *
 * @author petrovich
 */
class Command
{

    // Признак параметра команды: переменная
    const PARAMS_TAG_VAR = '$';
    //
    // Признак параметра команды: вложенный шаблон
    const PARAMS_TAG_TEMPLATE = '@';

    //

    /**
     * Компилирует (возвращает значение) строковый параметр команды шаблона.
     *
     * @param array $param Параметр команды
     *
     * @return string Скомпилированное значение параметра команды
     */
    private static function compileStringParam($param)
    {
        return $param;
    }

    /**
     * Компилирует (возвращает значение) параметр-переменную команды шаблона.
     *
     * @param array $param Параметр команды
     * @param array $viewData Массив данных, доступных для вывода в шаблоне.
     *
     * @return string Скомпилированное значение параметра команды
     */
    private static function compileVarParam($param, $viewData)
    {
        if (strpos($param, self::PARAMS_TAG_VAR) === false) {
            throw new \ErrorException('Command var parameter not valid');
        }
        $paramName = mb_substr($param, 1);

        // Если параметр - свойство объекта
        if (strpos($paramName, '->') !== false) {
            $paramParts = explode('->', $paramName);
            $obj = $viewData[$paramParts[0]];
            $prop = $paramParts[1];
            return $obj->$prop;
        }
        // Если параметр элемент массив с указанием индекса
        if ((strpos($paramName, '[') !== false) &&
                (strpos($paramName, ']') !== false)) {
            $paramParts = explode('[', str_replace(']', '', $paramName));
            $array = $viewData[$paramParts[0]];
            $index = $paramParts[1];
            return $array[$index];
        }
        return $viewData[$paramName];
    }

    /**
     * Компилирует (возвращает значение) параметр - вложенный шаблон.
     *
     * @param array $param Параметр команды
     * @param array $viewData Массив данных, доступных для вывода в шаблоне.
     *
     * @return string Скомпилированное значение параметра команды
     */
    private static function compileTemplateParam($param, $viewData)
    {
        if (strpos($param, self::PARAMS_TAG_TEMPLATE) === false) {
            throw new \ErrorException('Command template parameter not valid');
        }
        $viewFile = mb_substr($param, 1);
        // Вложенный шаблон. Отправляем его на рекурсивный цикл обработки
        return view()->render($viewFile, $viewData);
    }

    /**
     * Выполняет полную компиляцию параметра команды шаблона.
     *
     * @param array $param Параметр команды
     * @param array $viewData Массив данных, доступных для вывода в шаблоне.
     *
     * @return string Обработанный список параметров команды
     */
    private static function compileParam($param, $viewData)
    {
        if (strpos($param, self::PARAMS_TAG_TEMPLATE) === 0) {
            return self::compileTemplateParam($param, $viewData);
        } elseif (strpos($param, self::PARAMS_TAG_VAR) === 0) {
            return self::compileVarParam($param, $viewData);
        } else {
            return self::compileStringParam($param);
        }
    }

    /**
     * Выполняет обработку команды шаблона и возвращает результат.
     *
     * @param string $commandName Имя команды шаблона
     * @param array $params Параметры команды шаблона
     * @param array $viewData Массив данных, доступных для вывода в шаблоне.
     *
     * @return string Результат обработки команды
     */
    public static function run($commandName, $params, $viewData)
    {
        // Проверяем возможность вызова обработчика команды
        if (!method_exists(self::class, $commandName)) {
            throw new \ErrorException('Command method not exists');
        }
        return call_user_func([self::class, $commandName], $params, $viewData);
    }

    /**
     * Выполняет одноименную команду шаблона. Выводит значение указанного параметра.
     *
     * Формат команды: имя_команды(параметр)
     * Команда принимает только один обязательный параметр,
     * если указано несколько параметров, будет обработан только первый из них.
     *
     * Допустимый тип параметра: php-переменная, вложенный шаблон, строка.
     * Если тип параметра - php-переменная,
     * значение этой переменной должно быть совместимо с типом string.
     *
     * @param array $params Список параметров команды
     * @param array $viewData Массив данных, доступных для вывода в шаблоне.
     *
     * @return string Результат выполнения команды
     */
    private static function out($params, $viewData)
    {
        return self::compileParam($params[0], $viewData);
    }

    /**
     * Выполняет одноименную команду шаблона. Запускает цикл по заданному массиву.
     * На каждой итерации выводится заданный вложенный шаблон с
     * данными ключа и значения массива.
     *
     * Формат команды:
     * имя_команды($php-массив,
     * имя_переменной_в_цикле_ключ_массива (БЕЗ СИМВОЛА $ В НАЧАЛЕ!),
     * имя_переменной_в_цикле_значение_массива (БЕЗ СИМВОЛА $ В НАЧАЛЕ!),
     * вложенный_шаблон_для_вывода_в_каждой_итерации)
     *
     * Все параметры обязательны,
     * Если указано более 4 параметров, будут обработаны только 4 первых из них.
     *
     * Типы параметров:
     * php-массив: php-переменная типа array
     * имя_переменной_в_цикле_ключ_массива: php-переменная - будет выведен ключ массива
     * имя_переменной_в_цикле_значение_массива: php-переменная - будет выведено значение массива
     * вложенный_шаблон_для_вывода_в_каждой_итерации: путь ко вложенному шаблону
     *
     * @param array $params Список параметров команды
     * @param array $viewData Массив данных, доступных для вывода в шаблоне.
     *
     * @return string Результат выполнения команды
     */
    private static function loopforeach($params, $viewData)
    {
        if (strpos($params[0], self::PARAMS_TAG_VAR) === false) {
            throw new \ErrorException('Command foreach: "array" is not php-parameter');
        }
        $arr = self::compileVarParam($params[0], $viewData);
        $keyName = self::compileStringParam($params[1]);
        $valueName = self::compileStringParam($params[2]);
        $template = self::compileStringParam($params[3]);
        $out = '';
        // Индекс, начиная с 0
        $index = 0;
        // Номер итерации, начиная с 1
        $iteration = 1;
        foreach ($arr as $key => $value) {
            // Вложенный шаблон. Отправляем его на рекурсивный цикл обработки
            $childViewData = [
                $keyName => $key,
                $valueName => $value,
                'index' => $index,
                'iteration' => $iteration,
            ];
            $out .= self::compileTemplateParam($template, array_merge($childViewData, $viewData)) . PHP_EOL;
            $index++;
            $iteration++;
        }
        return $out;
    }

    /**
     * Выполняет одноименную команду шаблона. Запускает цикл с заданным количеством итераций.
     *
     * Формат команды:имя_команды(
     * начальный_индекс,
     * конечный_индекс,
     * значение_инкремента,
     * вложенный_шаблон_для_вывода_в_каждой_итерации)
     *
     * Условие цикла: начальный_индекс <= конечный_индекс
     *
     *
     * Типы параметров:
     * начальный_индекс,
     * конечный_индекс,
     * значение_инкремента - любой тип
     * вложенный_шаблон_для_вывода_в_каждой_итерации: путь ко вложенному шаблону
     *
     * @param array $params Список параметров команды
     * @param array $viewData Массив данных, доступных для вывода в шаблоне.
     *
     * @return string Результат выполнения команды
     */
    private static function loopfor($params, $viewData)
    {
        $start = self::compileParam($params[0], $viewData);
        $end = self::compileParam($params[1], $viewData);
        $increment = self::compileParam($params[2], $viewData);
        $template = self::compileStringParam($params[3]);
        $out = '';
        // Индекс, начиная с 0
        $index = 0;
        // Номер итерации, начиная с 1
        $iteration = 1;
        for ($i = $start; $i <= $end; $i = $i + $increment) {
            // Вложенный шаблон. Отправляем его на рекурсивный цикл обработки
            $childViewData = [
                'start' => $start,
                'end' => $end,
                'increment' => $increment,
                'index' => $index,
                'iteration' => $iteration,
            ];
            $out .= self::compileTemplateParam($template, array_merge($childViewData, $viewData)) . PHP_EOL;
            $index++;
            $iteration++;
        }
        return $out;
    }

    /**
     * Выполняет одноименную команду шаблона. Полностью аналогична команде out, за исключением того,
     * что содержимое первого параметра отображается, если значение второго параметра = true
     *
     * Формат команды: имя_команды(параметр, параметр_условие)
     * Команда принимает только два обязательных параметра,
     * если указано несколько параметров, будет обработано только два первых из них.
     *
     * Допустимый тип параметров: php-переменная, вложенный шаблон, строка.
     *
     * @param array $params Список параметров команды
     * @param array $viewData Массив данных, доступных для вывода в шаблоне.
     *
     * @return string Результат выполнения команды
     */
    private static function ifout($params, $viewData)
    {
        $bool = self::compileParam($params[1], $viewData);
        return $bool ? self::compileParam($params[0], $viewData) : '';
    }

    /**
     * Выполняет одноименную команду шаблона. Исполняет PHP-код.
     *
     * Для этой команды не используется парсинг параметров,
     * поэтому её имя должно предваряться знаком @: @phpeval
     *
     * Формат команды: @phpeval(исполняемый PHP-код для php-функции eval).
     * В исполняемом php-коде также доступна переменная
     * $viewData - массив данных, доступных для вывода в шаблоне.
     *
     * @param array $params Список параметров команды
     * @param array $viewData Массив данных, доступных для вывода в шаблоне.
     *
     * @return string Результат выполнения команды
     */
    private static function phpeval($params, $viewData)
    {
        $code = $params[0];
        // Пишем вывод в буфер
        ob_start();
        eval($code);
        $result = ob_get_contents();
        ob_end_clean();
        return $result;
    }

}
