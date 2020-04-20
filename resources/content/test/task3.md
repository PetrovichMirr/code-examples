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