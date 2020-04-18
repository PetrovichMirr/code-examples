<?php

namespace App\Core\ViewTemplate;

use App\Core\ViewTemplate\Command;

/**
 * Обработка шаблона.
 * Принцип работы шаблонизатора:
 * Динамические элементы выделяются в шаблоне тегами,
 * например <app_code>...</app_code>
 * Между этими тегами размещается команда.
 * Формат команды: имя_команды(параметр_1,параметр_2, ...)
 * Например, для вывода php-переменной $var в нужном месте шаблона нужно прописать:
 * <app_code>out($var)</app_code>
 *
 * Если перед именем команды стоит символ @, для такой команды
 * парсинг параметров не выполняется,
 * всё содержимое внутри скобок считается за один параметр
 *
 * Формат параметров:
 *
 * 1. Если имя параметра начинается с символа $, он трактуется,
 * как одноименная переменная, переданная шаблону в массиве данных.
 * Например, в шаблон был передан массив ['var' => $value], в этом случае
 * переменная $value будет доступна в шаблоне, как $var.
 * Параметр будет скомпилирован, как значение $value.
 *
 * Также php-переменная может быть объектом,
 * если в шаблоне вызывается его свойство,
 * например, если в шаблон переданы данные ['obj' => $obj],
 * в качестве параметра можно указать: $obj->prop.
 * Параметр будет скомпилирован, как значение $obj->prop.
 *
 * 2. Если имя параметра начинается с символа @, он трактуется, как вложенный шаблон
 * и представляет собой строку, содержащую путь к шаблону вида (относительно директории видов views).
 * Будет скомпилирован, как шаблон с заданными данными.
 *
 * 3. Если имя параметра не начинается с символов $ и не @, он трактуется
 * как обычная строка, содержащая имя этого параметра.
 * Параметр будет скомпилирован, как обычная строка, содержащая имя этого параметра.
 *
 * @author petrovich
 */
class Render
{

    // Открывающий тег динамического блока шаблона.
    // Вычисленные значения динамического блока перед выводом
    // обрабатываются для преобразования специальных символов
    // в HTML-сущности (функция htmlspecialchars)
    const TAG_CODE_OPEN = '<app_code>';
    //
    // Закрывающий тег динамического блока шаблона.
    // Вычисленные значения динамического блока перед выводом
    // обрабатываются для преобразования специальных символов
    // в HTML-сущности (функция htmlspecialchars)
    const TAG_CODE_CLOSE = '</app_code>';
    //
    // Открывающий тег "сырого" динамического блока шаблона.
    // Вычисленные значения ЭТОГО динамического блока  НЕ ОБРАБАТЫВАЮТСЯ перед выводом!
    // Использовать с осторожностью для предотвращения XSS-уязвимостей!
    const TAG_RAW_CODE_OPEN = '<app_raw_code>';
    //
    // Закрывающий тег "сырого" динамического блока шаблона.
    // Вычисленные значения ЭТОГО динамического блока  НЕ ОБРАБАТЫВАЮТСЯ перед выводом!
    // Использовать с осторожностью для предотвращения XSS-уязвимостей!
    const TAG_RAW_CODE_CLOSE = '</app_raw_code>';
    //
    // Открывающий тег исполняемого динамического блока шаблона.
    // В теге можно размещать любой php-код.
    // Результат выполнения этого кода будет являться содержимым блока.
    // Вычисленные значения ЭТОГО динамического блока  НЕ ОБРАБАТЫВАЮТСЯ перед выводом!
    // Использовать с осторожностью для предотвращения XSS-уязвимостей!
    const TAG_PHP_CODE_OPEN = '<app_php_code>';
    //
    // Закрывающий тег исполняемого динамического блока шаблона.
    // В теге можно размещать любой php-код.
    // Результат выполнения этого кода будет являться содержимым блока.
    // Вычисленные значения ЭТОГО динамического блока  НЕ ОБРАБАТЫВАЮТСЯ перед выводом!
    // Использовать с осторожностью для предотвращения XSS-уязвимостей!
    const TAG_PHP_CODE_CLOSE = '</app_php_code>';
    // Открывающая строка (или символ) списка параметров команды
    const PARAMS_OPEN = '(';
    //
    // Закрывающая строка (или символ) списка параметров команды
    const PARAMS_CLOSE = ')';
    //
    // Разделитель параметров команды
    const PARAMS_DELIMITER = ',';

    /**
     * Выполняет синтаксический разбор команды шаблона, выполняет её и возвращает результат.
     *
     * @param string $command Команда шаблона
     * @param array $viewData Массив данных, доступных для вывода в шаблоне.
     *
     * @return string Результат обработки команды
     */
    private function run($command, $viewData)
    {
        // Извлекаем параметры команды
        // Имя команды
        $pattern = '#\s*(?<command_name>\S+?)\s*\\' . self::PARAMS_OPEN .
                '(?<params>(\s*[^\s,]*\s*' . '\\' . self::PARAMS_DELIMITER .
                '?)*)\\' . self::PARAMS_CLOSE . '#';

        preg_match_all($pattern, $command, $matches);
        $commandName = $matches['command_name'][0];
        $strParams = $matches['params'][0];

        if (stripos($commandName, '@') === 0) {
            // Без парсинга параметров
            $commandName = substr($commandName, 1);
            $params[0] = $strParams;
        } else {
            // Список параметров
            $pattern = '#\s*(?<params>[^\s,]+)\s*' . '\\' . self::PARAMS_DELIMITER . '?#';
            preg_match_all($pattern, $strParams, $matches);
            $params = $matches['params'];
        }
        return Command::run($commandName, $params, $viewData);
    }

    /**
     * Обрабатывает содержимое шаблона, запускает команды в динамических блоках шаблона,
     * используя список команд, возвращённый функцией preg_match_all
     *
     * @param string $templateContent Содержимое шаблона.
     * @param array $matches Список команд, возвращённый функцией preg_match_all.
     * @param array $data Данные для вывода в шаблоне вида.
     * @param array $htmlspecialchars Определяет, обрабатывать ли вычисленное значение динамического блока функцией htmlspecialchars.
     *
     * @return string Обработанное содержимое
     */
    private function runCommands($templateContent, $matches, $data, $htmlspecialchars = true)
    {
        // Разбиваем шаблон на фрагменты, вставляем вычисленные значения динамических вставок
        $outContent = '';
        $contentStart = 0;
        foreach ($matches[0] as $index => $match) {
            $commandWithTags = $match[0];
            $position = $match[1];

            $partContent = substr($templateContent, $contentStart, $position - $contentStart);
            $contentStart = $position + strlen($commandWithTags);
            $outContent .= $partContent;
            $command = $matches['command'][$index][0];
            // Вставляем результат выполнения команды
            $outCommand = $this->run($command, $data);
            $outContent .= $htmlspecialchars ? htmlspecialchars($outCommand, ENT_QUOTES | ENT_HTML401) : $outCommand;
        }
        // Добавляем в вывод оставшуюся часть содержимого шаблона
        $partContent = substr($templateContent, $contentStart);
        $outContent .= $partContent;
        return $outContent;
    }

    /**
     * Выполняет обработку содержимого шаблона (выводит данные в шаблон вида) и возвращает результат.
     * Формат данных для вывода: [ имя_переменной_шаблона => значение_переменной_шаблона]
     *
     * @param string $templateContent Содержимое шаблона
     * @param array $data Данные для вывода в шаблоне вида.
     *
     * @return string Результат обработки шаблона
     */
    public function handle($templateContent, $data = [])
    {
        // Находим с помощью регулярного выражения все вхождения динамических элементов шаблона
        //
        // 1. НЕ обрабатываемые динамические блоки
        $pattern = '#' . self::TAG_RAW_CODE_OPEN . '(?<command>.*?)' . self::TAG_RAW_CODE_CLOSE . '#';
        preg_match_all($pattern, $templateContent, $matches, PREG_OFFSET_CAPTURE);
        // Обрабатываем содержимое, выполняя команды в динамических блоках шаблона
        $outContent = $this->runCommands($templateContent, $matches, $data, false);

        // 2. Обрабатываемые (htmlspecialchars) динамические блоки
        $pattern = '#' . self::TAG_CODE_OPEN . '(?<command>.*?)' . self::TAG_CODE_CLOSE . '#';
        preg_match_all($pattern, $outContent, $matches, PREG_OFFSET_CAPTURE);
        // Обрабатываем содержимое, выполняя команды в динамических блоках шаблона
        return $this->runCommands($outContent, $matches, $data);
    }

}
