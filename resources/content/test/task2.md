Сначала нам понадобятся три таблицы базы данных:
ученики, учителя и классы.
Пусть эти таблицы назывываются:
`students`, `teachers`, `classes`.

*Таблица № 1 "классы", `classes`*

Столбцы таблицы `classes`:

- `id` - первичный ключ, целочисленный тип, беззнаковый, автоинкремент,
значение по умолчанию отсутствует, не может быть `null`;
- `name` - любое имя класса (например, 5А класс и т.п.),
строковый тип, значение по умолчанию отсутствует, не может быть `null`,
возможно добавление индекса для поиска в данной таблице по имени класса;

*Таблица № 2 "ученики", `students`*

Столбцы таблицы `students`:

- `id` - первичный ключ, целочисленный тип, беззнаковый, автоинкремент,
значение по умолчанию отсутствует, не может быть `null`;
- `first_name` - имя ученика, строковый тип, значение по умолчанию отсутствует,
не может быть `null`,
возможно добавление индекса для поиска в данной таблице по имени ученика;
- `last_name` - фамилия ученика, строковый тип, значение по умолчанию отсутствует,
не может быть `null`,
возможно добавление индекса для поиска в данной таблице по фамилии ученика;
- `class_id` - ID класса из таблицы `classes`, целочисленный тип, беззнаковый,
значение по умолчанию отсутствует, не может быть `null`,
возможно добавление индекса для поиска в данной таблице по ID класса.

*Таблица № 3 "учителя", `teachers`*

Столбцы таблицы `teachers`:

- `id` - первичный ключ, целочисленный тип, беззнаковый, автоинкремент,
значение по умолчанию отсутствует, не может быть `null`;
- `first_name` - имя учителя, строковый тип, значение по умолчанию отсутствует,
не может быть `null`,
возможно добавление индекса для поиска в данной таблице по имени учителя;
- `last_name` - фамилия учителя, строковый тип, значение по умолчанию отсутствует,
не может быть `null`,
возможно добавление индекса для поиска в данной таблице по фамилии учителя.

Теперь нам понадобится ещё одна таблица базы данных для отражения связи
"многие ко многим" между учителями и классами.
Эта таблица будет представлять список классов и список
учителей, преподающих в этих классах. Получается что-то вроде рабочего
графика или таблицы учебной нагрузки. В общем, не будем долго думать над
названием, назовём её просто расписание, `timetable`.

*Таблица № 4 "расписание", `timetable`*

Столбцы таблицы `timetable`:

- `id` - первичный ключ, целочисленный тип, беззнаковый, автоинкремент,
значение по умолчанию отсутствует, не может быть `null`;
- `teacher_id` - ID учителя из таблицы `teachers`, целочисленный тип, беззнаковый,
значение по умолчанию отсутствует, не может быть `null`,
возможно добавление индекса для поиска в данной таблице по ID учителя;
- `class_id` - ID класса из таблицы `classes`, целочисленный тип, беззнаковый,
значение по умолчанию отсутствует, не может быть `null`,
возможно добавление индекса для поиска в данной таблице по ID класса.

Можно упомянуть про ограничения внешних ключей. 
Они могут быть применены для рассмотренных выше четырёх таблиц.