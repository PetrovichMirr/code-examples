<?php

namespace App\Core;

/**
 * Работа с базой данных
 *
 * @author petrovich
 */
class DB
{

    /**
     * Имя таблицы базы данных
     *
     * @var string
     */
    private $table;

    /**
     * SQL-запрос
     *
     * @var string
     */
    private $sql;

    /**
     * Массив связанных именованных или
     * неименованных псевдопеременных для подготовленного SQL-запроса.
     *
     * @var array
     */
    private $binds = [];

    /**
     * Экземпляр подготовленного запроса
     *
     * @var \PDOStatement
     */
    private $pdoStatement;

    /**
     * Имя класса для создания экземпляров, связанных с результирующим набором при $fetch_style = \PDO::FETCH_CLASS
     *
     * @var string
     */
    private $classname;

    /**
     * Аргументы конструктора класса для создания экземпляров, связанных с результирующим набором при $fetch_style = \PDO::FETCH_CLASS
     *
     * @var array
     */
    private $ctorargs = [];

    /**
     * ID последней вставленной строки или значение последовательности (ответ PDO::lastInsertId)
     *
     * @var string
     */
    private $lastInsertId;

    /**
     * Создаёт экземпляр класса
     *
     * @param string $table Имя таблицы
     *
     * @return this
     */
    public function __construct($table = null)
    {
        $this->setTable($table);
    }

    /**
     * Возвращает ID последней вставленной строки или значение последовательности (ответ PDO::lastInsertId)
     *
     * @return string
     */
    public function getLastInsertId()
    {
        return $this->lastInsertId;
    }

    /**
     * Устанавливает имя класса для создания экземпляров, связанных с результирующим набором при $fetch_style = \PDO::FETCH_CLASS
     *
     * @param string $value Имя класса
     *
     * @return this
     */
    public function setClassname($value)
    {
        $this->classname = $value;
        return $this;
    }

    /**
     * Возвращает имя класса для создания экземпляров, связанных с результирующим набором при $fetch_style = \PDO::FETCH_CLASS
     *
     * @return string Имя класса
     */
    public function getClassname()
    {
        return $this->classname;
    }

    /**
     * Устанавливает аргументы конструктора класса для создания экземпляров, связанных с результирующим набором при $fetch_style = \PDO::FETCH_CLASS
     *
     * @param array $value Аргументы конструктора класса
     *
     * @return this
     */
    public function setCtorargs($value)
    {
        $this->ctorargs = $value;
        return $this;
    }

    /**
     * Возвращает аргументы конструктора класса для создания экземпляров, связанных с результирующим набором при $fetch_style = \PDO::FETCH_CLASS
     *
     * @return array Аргументы конструктора класса
     */
    public function getCtorargs()
    {
        return $this->ctorargs;
    }

    /**
     * Возвращает используемый SQL-запрос к базе данных.
     * Если параметр $sql не указан, используется текущий SQL-запрос,
     * подготовленный цепочкой методов select, ...
     *
     * @param string $sql SQL - запрос. Если параметр не указан, используется текущий SQL-запрос
     *
     * @return string Используемый SQL-запрос к базе данных
     */
    private function getCurrentSql($sql = null)
    {
        return isset($sql) ? $sql : $this->getSql();
    }

    /**
     * Выполняет подготовленный запрос к базе данных.
     * Если параметр $sql не указан, используется текущий SQL-запрос,
     * подготовленный цепочкой методов select, ...
     *
     * @param string $sql SQL - запрос. Если параметр не указан, используется текущий SQL-запрос
     *
     * @return this
     */
    public function execute($sql = null)
    {
        $dsn = APP_DB_DRIVER . ':host=' . APP_DB_HOST . ';dbname=' . getenv('APP_DB_NAME') . ';charset=' . APP_DB_CHARSET;
        $pdo = new \PDO($dsn, getenv('APP_DB_USER'), getenv('APP_DB_PASSWORD'));
        $this->pdoStatement = $pdo->prepare($this->getCurrentSql($sql));
        if (!$this->pdoStatement->execute($this->binds)) {
            $errorInfo = $this->pdoStatement->errorInfo();
            throw new \ErrorException('PDOStatement error. SQLSTATE ' .
                    $errorInfo[0] . '. Driver error code ' .
                    $errorInfo[1] . '. ' . $errorInfo[2]);
        }
        $this->lastInsertId = $pdo->lastInsertId();
        return $this;
    }

    /**
     * Подготовка FetchMode, используется в функциях fetch и fetchAll
     * Если FetchMode был установлен в этом методе, то
     * при вызове функций fetch и fetchAll параметр $fetchStyle не нужно указывать,
     * иначе при $fetch_style = \PDO::FETCH_CLASS не будут создаваться заданные классы.
     *
     * @param string $fetchStyle Определяет содержимое возвращаемого массива. Значение - одна из констант \PDO::FETCH_*
     * @param string $classname Имя класса для создания экземпляров, связанных с результирующим набором при $fetch_style = \PDO::FETCH_CLASS
     * @param array $ctorargs Аргументы конструктора класса для создания экземпляров, связанных с результирующим набором при $fetch_style = \PDO::FETCH_CLASS
     *
     * @return bool Если true, FetchMode был уже установлен в этом методе и при вызове функций fetch и fetchAll параметр $fetchStyle не нужно указывать.
     */
    private function prepareFetchMode($fetchStyle = \PDO::FETCH_CLASS, $classname = null, $ctorargs = [])
    {
        $useClassname = isset($classname) ? $classname : $this->classname;
        $useCtorargse = !empty($ctorargs) ? $ctorargs : $this->ctorargs;
        if ($fetchStyle == \PDO::FETCH_CLASS && (isset($useClassname))) {
            $this->pdoStatement->setFetchMode(\PDO::FETCH_CLASS, $useClassname, $useCtorargse);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Возвращает следующую строку результирующего набора.
     *
     * @param string $fetchStyle Определяет содержимое возвращаемого массива. Значение - одна из констант \PDO::FETCH_*
     * @param string $classname Имя класса для создания экземпляров, связанных с результирующим набором при $fetch_style = \PDO::FETCH_CLASS
     * @param array $ctorargs Аргументы конструктора класса для создания экземпляров, связанных с результирующим набором при $fetch_style = \PDO::FETCH_CLASS
     *
     * @return mixed Следующая строка результирующего набора
     */
    public function fetch($fetchStyle = \PDO::FETCH_CLASS, $classname = null, $ctorargs = [])
    {
        $prepareFetchMode = $this->prepareFetchMode($fetchStyle, $classname, $ctorargs);
        if ($prepareFetchMode) {
            return $this->pdoStatement->fetch();
        } else {
            return $this->pdoStatement->fetch($fetchStyle);
        }
    }

    /**
     * Возвращает массив, содержащий все строки результирующего набора.
     *
     * @param string $fetchStyle Определяет содержимое возвращаемого массива. Значение - одна из констант \PDO::FETCH_*
     * @param string $classname Имя класса для создания экземпляров, связанных с результирующим набором при $fetch_style = \PDO::FETCH_CLASS
     * @param array $ctorargs Аргументы конструктора класса для создания экземпляров, связанных с результирующим набором при $fetch_style = \PDO::FETCH_CLASS
     *
     * @return array Массив, содержащий все строки результирующего набора
     */
    public function fetchAll($fetchStyle = \PDO::FETCH_CLASS, $classname = null, $ctorargs = [])
    {
        $prepareFetchMode = $this->prepareFetchMode($fetchStyle, $classname, $ctorargs);
        if ($prepareFetchMode) {
            return $this->pdoStatement->fetchAll();
        } else {
            return $this->pdoStatement->fetchAll($fetchStyle);
        }
    }

    /**
     * Возвращает массив, содержащий все строки таблицы БД
     *
     * @return array Массив, содержащий все строки результирующего набора
     */
    public function all()
    {
        return $this->select()->where()->execute()->fetchAll();
    }

    /**
     * Устанавливает имя таблицы для выполнения SQL-запроса
     *
     * @param string $table Имя таблицы для выполнения SQL-запроса
     *
     * @return this
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Возвращает имя таблицы для выполнения SQL-запроса
     *
     * @return string Имя таблицы для выполнения SQL-запроса
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Возвращает содержимое текущего SQL-запроса
     *
     * @return string Содержимое текущего SQL-запроса
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * Определяет, есть ли в БД есть строки, удовлетворяющие заданному sql-запросу.
     * Для этого выполняет запрос EXISTS, в этом случае
     * заданный sql-запрос выполняется, как подзапрос EXISTS.
     *
     * Если параметр $sql не указан, используется текущий SQL-запрос,
     * подготовленный цепочкой методов select, ...
     *
     * @param string $sql SQL - запрос. Если параметр не указан, используется текущий SQL-запрос
     *
     * @return bool Возвращает true, если в БД есть строки, удовлетворяющие текущему sql-запросу.
     */
    public function existsQuery($sql = null)
    {
        $sqlExists = "SELECT (EXISTS ({$this->getCurrentSql($sql)})) AS result";
        $result = $this->execute($sqlExists)->fetch(\PDO::FETCH_ASSOC);
        return $result['result'] == 1;
    }

    /**
     * Устанавливает список парамтеров для SET части SQL-запроса
     * (используется в INSERT / UPDATE)
     * по заданному списку значений столбцов.
     * Содержимое $values должно представлять собой массив
     * вида [col_name => col_value, ...]
     *
     * В параметре $binds может указываться массив
     * связанных именованных или неименованных псевдопеременных
     * для подготовленного SQL-запроса.
     * Пример именованных псевдопеременных:
     * [':calories' => $calories, ':colour' => $colour]
     * Пример неименованных псевдопеременных:
     * [$calories, $colour]
     *
     * Если используются псевдопеременные, все параметры должны быть переданы через них!
     *
     * @param array $values Массив значений столбцов для вставки
     * @param string $binds Массив связанных именованных или неименованных псевдопеременных для подготовленного SQL-запроса.
     *
     * @return this
     */
    private function setForInsertOrUpdate($values, $binds = [])
    {
        $this->binds = $binds;
        $strValues = '';
        foreach ($values as $name => $value) {
            if (empty($binds)) {
                $strValues .= empty($strValues) ? "{$name} = \"{$value}\"" : ", {$name} = \"{$value}\"";
            } else {
                $strValues .= empty($strValues) ? "{$name} = {$value}" : ", {$name} = {$value}";
            }
        }
        return $strValues;
    }

    /**
     * Устанавливает содержимое части SQL-запроса INSERT INTO ... SET ...
     * по заданному списку значений столбцов.
     * Содержимое $values должно представлять собой массив
     * вида [col_name => col_value, ...]
     *
     * В параметре $binds может указываться массив
     * связанных именованных или неименованных псевдопеременных
     * для подготовленного SQL-запроса.
     * Пример именованных псевдопеременных:
     * [':calories' => $calories, ':colour' => $colour]
     * Пример неименованных псевдопеременных:
     * [$calories, $colour]
     *
     * Если используются псевдопеременные, все параметры должны быть переданы через них!
     *
     * @param array $values Массив значений столбцов для вставки
     * @param string $binds Массив связанных именованных или неименованных псевдопеременных для подготовленного SQL-запроса.
     *
     * @return this
     */
    public function insert($values, $binds = [])
    {
        $this->sql = "INSERT INTO {$this->getTable()} SET {$this->setForInsertOrUpdate($values, $binds)}";
        return $this;
    }

    /**
     * Устанавливает содержимое части SQL-запроса UPDATE ... SET ...
     * по заданному списку значений столбцов.
     * Содержимое $values должно представлять собой массив
     * вида [col_name => col_value, ...]
     *
     * В параметре $binds может указываться массив
     * связанных именованных или неименованных псевдопеременных
     * для подготовленного SQL-запроса.
     * Пример именованных псевдопеременных:
     * [':calories' => $calories, ':colour' => $colour]
     * Пример неименованных псевдопеременных:
     * [$calories, $colour]
     *
     * Если используются псевдопеременные, все параметры должны быть переданы через них!
     *
     * @param array $values Массив значений столбцов для обновления
     * @param string $binds Массив связанных именованных или неименованных псевдопеременных для подготовленного SQL-запроса.
     *
     * @return this
     */
    public function update($values, $binds = [])
    {
        $this->sql = "UPDATE {$this->getTable()} SET {$this->setForInsertOrUpdate($values, $binds)}";
        return $this;
    }

    /**
     * Устанавливает содержимое части SQL-запроса DELETE FROM ...
     *
     * @return this
     */
    public function delete()
    {
        $this->sql = "DELETE FROM {$this->getTable()}";
        return $this;
    }

    /**
     * Устанавливает содержимое части SQL-запроса SELECT
     *
     * @param string $sql Содержимое части SQL-запроса SELECT
     *
     * @return this
     */
    public function select($sql = '*')
    {
        $this->sql = 'SELECT ' . $sql . ' FROM ' . $this->getTable();
        return $this;
    }

    /**
     * Устанавливает содержимое части SQL-запроса WHERE.
     *
     * В параметре $binds может указываться массив
     * связанных именованных или неименованных псевдопеременных
     * для подготовленного SQL-запроса.
     * Пример именованных псевдопеременных:
     * [':calories' => $calories, ':colour' => $colour]
     * Пример неименованных псевдопеременных:
     * [$calories, $colour]
     *
     * @param string $sql Содержимое части SQL-запроса WHERE
     * @param string $binds Массив связанных именованных или неименованных псевдопеременных для подготовленного SQL-запроса.
     *
     * @return this
     */
    public function where($sql = 1, $binds = [])
    {
        $this->sql .= ' WHERE ' . $sql;
        $this->binds = $binds;
        return $this;
    }

    /**
     * Устанавливает содержимое части SQL-запроса GROUP BY.
     *
     * @param string $sql Содержимое части SQL-запроса GROUP BY.
     * @param string $dir Направление сортировки, ASC или DESC.
     *
     * @return this
     */
    public function groupBy($sql, $dir = 'ASC')
    {
        $this->sql .= ' GROUP BY ' . $sql . ($dir == 'ASC' ? ' ASC' : ' DESC');
        return $this;
    }

    /**
     * Устанавливает содержимое части SQL-запроса ORDER BY.
     *
     * @param string $sql Содержимое части SQL-запроса ORDER BY.
     * @param string $dir Направление сортировки, ASC или DESC.
     *
     * @return this
     */
    public function orderBy($sql, $dir = 'ASC')
    {
        $this->sql .= ' ORDER BY ' . $sql . ($dir == 'ASC' ? ' ASC' : ' DESC');
        return $this;
    }

    /**
     * Устанавливает максимальное количество строк для вывода (LIMIT).
     *
     * @param int $limit Максимальное количество строк для вывода.
     *
     * @return this
     */
    public function limit($limit = null)
    {
        if (!isset($limit)) {
            return $this;
        }
        $this->sql .= ' LIMIT ' . $limit;
        return $this;
    }

    /**
     * Устанавливает смещение (OFFSET).
     *
     * @param int $offset Смещение.
     *
     * @return this
     */
    public function offset($offset = 0)
    {
        $this->sql .= ' OFFSET ' . $offset;
        return $this;
    }

    /**
     * Возвращает данные с именованными псевдопеременными
     * для подготовленного запроса.
     * Формат выходных данных:
     * [ 'aliases' => массив_с_псевдопеременными, 'binds' => массив_с_реальными_значениями ]
     *
     * @param array $values Массив значений, для которых необходимо подготовить запрос. Формат массива: [ name => value, ... ]
     *
     * @return array Возвращает данные с именованными псевдопеременными для подготовленного запроса.
     */
    public function getBinds($values)
    {
        // Массив с реальными значениями
        $binds = [];
        // Массив с псевдопеременными
        $aliases = [];
        foreach ($values as $name => $value) {
            $alias = ':' . $name;
            $binds[$alias] = $value;
            $aliases[$name] = $alias;
        }
        return ['aliases' => $aliases, 'binds' => $binds];
    }

    /**
     * Устанавливает содержимое части SQL-запроса SELECT для
     * нахождения количества записей, удовлетворяющих запросу
     *
     * @return int
     */
    private function selectCount()
    {
        $sql = 'COUNT(*) AS result';
        return $this->select($sql);
    }

    /**
     * Возвращает количество всех записей таблицы
     *
     * @return int
     */
    public function count()
    {
        $result = $this->selectCount()->where()->execute()->fetch(\PDO::FETCH_ASSOC);
        return $result['result'];
    }

}
