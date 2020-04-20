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