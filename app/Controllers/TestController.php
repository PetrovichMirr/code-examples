<?php

namespace App\Controllers;

/**
 * Контроллер для тестового задания
 *
 * @author petrovich
 */
class TestController
{

    /**
     * Возвращает содержимое markdown - файла
     *
     * @param string $markdownFile Путь к markdown - файлу
     *
     * @return string Содержимое markdown - файла
     */
    public function getMarkdownContent($markdownFile)
    {
        // Парсим файл markdown
        $parsedown = new \Parsedown();
        if (!file_exists($markdownFile)) {
            return '';
        }
        return $parsedown->text(file_get_contents($markdownFile));
    }

    /**
     * Вывод страницы с результатами
     * выполнения тестового задания
     *
     * @return mixed Ответ приложения
     */
    public function testIndex()
    {
        $data = view()->render('pages/test.php', [
            'fibonacci' => $this->fibonacci(),
            'database' => $this->databaseSchema(),
            'decryption' => $this->decryption(),
        ]);
        return response()->setBody($data);
    }

    /**
     * Решение задачи № 1.
     *
     * @return array Данные решения задачи
     */
    private function fibonacci()
    {
        // Размер массива
        $arrCount = 6;
        // Инициализируем пустой массив
        $arr = [];
        // Два последних числа
        $lastNumber1 = 0; // число перед предыдущим
        $lastNumber2 = 0; // предыдущее
        // Наполняем массив
        for ($col = 0; $col < $arrCount; $col++) {
            for ($row = 0; $row < $arrCount; $row++) {
                // Если предыдущее число $lastNumber2 равно нулю,
                // то текущее число = 1,
                // иначе текущее число = сумме двух предыдущих
                $currentNumber = $lastNumber2 == 0 ? 1 : $lastNumber1 + $lastNumber2;

                $arr[$row][$col] = $currentNumber;

                $lastNumber1 = $lastNumber2;
                $lastNumber2 = $currentNumber;
            }
        }
        // Индекс строки с числом диагонали
        $diagonal = 5;
        // Сумма чисел диагонали
        $sum = 0;
        for ($col = 0; $col < $arrCount; $col++) {
            $sum += $arr[$diagonal][$col];
            $diagonal--;
        }
        return [
            'arr' => $arr,
            'sum' => $sum,
            'code' => $this->getMarkdownContent(APP_PATH_DIR_RESOURCES . '/content/test/task1.md'),
        ];
    }

    /**
     * Решение задачи № 2.
     *
     * @return string Данные решения задачи
     */
    private function databaseSchema()
    {
        return [
            'code' => $this->getMarkdownContent(APP_PATH_DIR_RESOURCES . '/content/test/task2.md'),
        ];
    }

    /**
     * Дополнительная функция для решения задачи № 3. Осуществляет поиск
     * в заданной строке и возвращает аргумент специального обозначения. Заданная
     * строка должна начинаться с аргумента (без спец. обозначения)
     *
     * @param string $str Строка для поиска аргумента
     * @return int Значение аргумента
     */
    private function getParameter($str)
    {
        // Поиск аргумента - целого числа
        $pattern = '#^(?<parameter>\d+).*#';
        preg_match_all($pattern, $str, $matches);
        return $matches['parameter'][0];
    }

    /**
     * Решение задачи № 3.
     *
     * @return string Данные решения задачи
     */
    private function decryption()
    {
        // Зашифрованное содержимое
        $encryptContent = '->11гe+20∆∆A+4µcњil->5•Ћ®†Ѓ p+5f-7Ќ¬f pro+10g+1悦ra->58->44m+1*m+2a喜er!';
        // Расшифрованное содержимое
        $decryptContent = '';
        // Специальные обозначения
        //"->", "+", "-".
        $specialGoTo = '->';
        $specialSkipForward = '+';
        $specialSkipBackward = '-';
        // Индекс текущего символа строки, нумерация с 0
        $currentIndex = 0;
        // Цикл расшифровки заканчиваем тогда,
        // когда индекс текущего расшифруемого символа выйдет за пределы строки
        // Длина строки, используем функции mb_*
        // для корректного учёта многобайтных символов.
        $encryptContentLength = mb_strlen($encryptContent);
        while ($currentIndex < $encryptContentLength) {
            // Проверка наличия специальных обозначений в текущей позиции
            if (mb_strpos($encryptContent, $specialGoTo, $currentIndex) === $currentIndex) {
                // Значение аргумента
                $parameter = $this->getParameter(mb_substr($encryptContent, $currentIndex + 2));
                $currentIndex = $parameter;
            } elseif (mb_strpos($encryptContent, $specialSkipForward, $currentIndex) === $currentIndex) {
                // Значение аргумента
                $parameter = $this->getParameter(mb_substr($encryptContent, $currentIndex + 1));
                $currentIndex = $currentIndex + 1 + mb_strlen($parameter) + $parameter;
            } elseif (mb_strpos($encryptContent, $specialSkipBackward, $currentIndex) === $currentIndex) {
                // Значение аргумента
                $parameter = $this->getParameter(mb_substr($encryptContent, $currentIndex + 1));
                $currentIndex = $currentIndex + 1 + mb_strlen($parameter) - $parameter;
            } else {
                $decryptContent .= mb_substr($encryptContent, $currentIndex, 1);
                $currentIndex++;
            }
        }
        return [
            'decrypt' => $decryptContent,
            'code' => $this->getMarkdownContent(APP_PATH_DIR_RESOURCES . '/content/test/task3.md'),
        ];
    }

}
