<!DOCTYPE html>
<html lang="ru">
    <head>
        <title>Тестовое задание</title>
    <app_raw_code>out(@chunks/head.php)</app_raw_code>
</head>
<body>
    <div class="bg-light">
        <div class="container">
            <app_raw_code>out(@chunks/navbar.php)</app_raw_code>
        </div>
    </div>
    <div class="container py-4">
        <h1>Результаты выполнения тестового задания</h1>
        <p class="font-weight-bold">Задание № 1.</p>
        <p>
            При помощи языка PHP создайте двумерный массив размером 6х6,
            заполните его числами из последовательности Фибоначчи таким образом,
            чтобы в углу [0][0] была единица, в ячейке [1][0] была единица,
            в ячейке [2][0] была цифра 2.
            Найдите сумму чисел находящихся на диагонали [5][0]-[0][5]
            Ответом является число.
        </p>
        <p class="font-weight-bold">Ответ</p>
        <p>Массив:</p>
        <app_raw_code>@phpeval(foreach ($viewData['fibonacci']['arr'] as $cols) { foreach ($cols as $cell) { echo $cell . ',&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; } echo '<br>'; })</app_raw_code>
        <p class="mt-2">Сумма диагонали [5][0] - [0][5]: <app_code>@phpeval(return number_format($viewData['fibonacci']['sum'], 0, '.', ' ');)</app_code></p>
        <p class="font-weight-bold">Код</p>
        <app_raw_code>@phpeval(return $viewData['fibonacci']['code'];)</app_raw_code>
        
        <p class="font-weight-bold">Задание № 2.</p>
        <p>
            Нарисуйте в свободной форме схему следующей БД:<br>
            1) есть ученики, учителя и классы<br>
            2) каждый ученик учится в каком-то классе<br>
            3) учитель может преподавать в одном или более классах<br>
            4) в одном классе может преподавать один или более учителей<br>
        </p>
        <p class="font-weight-bold">Ответ</p>
        <p><app_raw_code>@phpeval(return $viewData['database']['code'];)</app_raw_code></p>
    
        <p class="font-weight-bold">Задание № 3.</p>
        <p>
            Дан шифр ->11гe+20∆∆A+4µcњil->5•Ћ®†Ѓ p+5f-7Ќ¬f pro+10g+1悦ra->58->44m+1*m+2a喜er!<br>
            Правила его расшифровки следующие:<br>
             - Начинать чтение нужно с крайнего левого символа и двигаться вправо.<br>
             - Если вы сталкиваетесь с любым символом, кроме специальных обозначений,<br>
             то данный символ без изменений попадает в результирующую строчку.<br>

             - Специальными обозначениями являются "->", "+", "-".<br>
             После специального обозначения всегда идет число, являющееся аргументом.<br>
             - "->" — вам необходимо перейти к символу с номером, записанном в аргументе (счет начинается с 0).<br>
             - "+" — пропустить столько символов, сколько записано в аргументе. Отсчет начинается после аргумента.<br>
             - "-" — аналогично, но перемещение происходит назад (влево)<br>
            Программа должна быть написана на PHP. Ответом является строчка.<br>
        </p>
        <p class="font-weight-bold">Ответ</p>
        <p><app_code>@phpeval(return $viewData['decryption']['decrypt'];)</app_code></p>
        <p class="font-weight-bold">Код</p>
        <app_raw_code>@phpeval(return $viewData['decryption']['code'];)</app_raw_code>
    </div>
<app_raw_code>out(@chunks/footer.php)</app_raw_code>
<app_raw_code>out(@chunks/js.php)</app_raw_code>
</body>
</html>
