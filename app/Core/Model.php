<?php

namespace App\Core;

use App\Core\DB;

/**
 * Модель
 *
 * @author petrovich
 */
class Model extends DB
{

    /**
     * Имя столбца с первичным ключом
     *
     * @var string
     */
    private $primaryKeyName = 'id';

    /**
     * Атрибуты модели
     *
     * @var array
     */
    private $attributes = [];

    /**
     * Настройки атрибутов модели
     *
     * @var array
     */
    private $attributesSettings = [];

    /**
     * Имя атрибута "время создания"
     *
     * @var string
     */
    private $createdAtColName;

    /**
     * Имя атрибута "время обновления"
     *
     * @var string
     */
    private $updatedAtColName;

    /**
     * Создаёт экземпляр класса
     *
     * @param string $table Имя таблицы
     *
     * @return this
     */
    public function __construct($table = null)
    {
        parent::__construct($table);
        // Используем позднее статическое связывание
        // для того, чтобы установить имя класса-потомка, вызвавшего
        // метод, а не класса-родителя, в котором определен этот код.
        // Грубо говоря, в данном случае мы пишем "static::class", а не "self::class"
        $this->setClassname(static::class);
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
        if (isset($table)) {
            $this->setCtorargs([$table]);
        }
        return parent::setTable($table);
    }

    /**
     * Возвращает экземпляр модели по первичному ключу.
     * Если модель не найдена, возвращает null
     *
     * @param int $primaryKeyValue Значение первичного ключа записи
     *
     * @return App\Core\Model|null Экземпляр модели или null, если модель не найдена
     */
    public function find($primaryKeyValue)
    {
        $where = "{$this->getPrimaryKeyName()} = {$primaryKeyValue}";
        $data = $this->select()->where($where)->execute()->fetch();
        return empty($data) ? null : $data;
    }

    /**
     * Загружает значения атрибутов модели из базы данных по первичному ключу
     *
     * @param int $primaryKeyValue Значение первичного ключа записи
     *
     * @return void
     */
    public function load($primaryKeyValue)
    {
        $where = "{$this->getPrimaryKeyName()} = {$primaryKeyValue}";
        $data = $this->select()->where($where)->execute()->fetch(\PDO::FETCH_ASSOC);
        if (empty($data)) {
            throw new \ErrorException('Unable to load model data by primary key ' . $primaryKeyValue);
        }
        $this->setAttributes($data);
    }

    /**
     * Обновляет значения атрибутов модели из базы данных
     *
     * @return void
     */
    public function refresh()
    {
        $primaryKeyValue = $this->getPrimaryKeyValue();
        if (!isset($primaryKeyValue)) {
            return;
        }
        $this->load($primaryKeyValue);
    }

    /**
     * Удаляет из базы данных записи, соответствующие модели.
     * Если указан параметр $primaryKeyValue,
     * производится удаление записей модели по указанному значению первичного ключа.
     * Если параметр $primaryKeyValue не указан, удаляются записи текущей модели.
     *
     * @param int $primaryKeyValue Значение первичного ключа записи
     *
     * @return void
     */
    public function delete($primaryKeyValue = null)
    {
        $usedPrimaryKeyValue = isset($primaryKeyValue) ? $primaryKeyValue : $this->getPrimaryKeyValue();
        if (!isset($usedPrimaryKeyValue)) {
            return;
        }
        $where = "{$this->getPrimaryKeyName()} = {$usedPrimaryKeyValue}";
        parent::delete()->where($where)->execute();
    }

    /**
     * Присваивает атрибуту "время обновления" значение текущего времени
     *
     * @return void
     */
    private function setUpdatedAtAttribute()
    {
        // Проверяем наличие имени атрибута "время обновления",
        // при его наличии - устанавливаем его значение
        if (!empty($this->getUpdatedAtColName())) {
            $this->setAttribute($this->getUpdatedAtColName(), date('Y-m-d H:i:s'));
        }
    }

    /**
     * Присваивает атрибуту "время создания" значение текущего времени
     *
     * @return void
     */
    private function setCreatedAtAttribute()
    {
        // Проверяем наличие имени атрибута "время создания",
        // при его наличии - устанавливаем его значение
        if (!empty($this->getCreatedAtColName())) {
            $this->setAttribute($this->getCreatedAtColName(), date('Y-m-d H:i:s'));
        }
    }

    /**
     * Сохраняет значения атрибутов модели в базе данных
     *
     * @return void
     */
    public function save()
    {
        // Определяем, есть ли записи в БД
        if ($this->exists()) {
            // Обновление
            $this->setUpdatedAtAttribute();
            $attributes = $this->getAttributes();
            // Удаляем из данных для обновления первичный ключ
            unset($attributes[$this->getPrimaryKeyName()]);
            $where = "{$this->getPrimaryKeyName()} = {$this->getPrimaryKeyValue()}";
            $bindsData = $this->getBinds($attributes);
            $this->update($bindsData['aliases'], $bindsData['binds'])->where($where, $bindsData['binds'])->execute();
        } else {
            // Вставка
            $this->setCreatedAtAttribute();
            $this->setUpdatedAtAttribute();
            $bindsData = $this->getBinds($this->getAttributes());
            $this->insert($bindsData['aliases'], $bindsData['binds'])->execute();
            $this->setPrimaryKeyValue($this->getLastInsertId());
            // Обновляем данные модели
            $this->refresh();
        }
    }

    /**
     * Определяет существует ли модель в базе данных.
     * Проверка осуществляется по первичному ключу модели,
     * т.е., метод определяет присутствует ли
     * первичный ключ модели в базе данных.
     *
     * @return bool Возвращает true, если модель существует в базе данных.
     */
    public function exists()
    {
        $primaryKeyValue = $this->getPrimaryKeyValue();
        if (!isset($primaryKeyValue)) {
            return false;
        }
        $where = "{$this->getPrimaryKeyName()} = {$primaryKeyValue}";
        return $this->select()->where($where)->existsQuery();
    }

    /**
     * Устанавливает значение списка атрибутов
     *
     * @param array $value Массив атрибутов
     *
     * @return this
     */
    public function setAttributes($value)
    {
        $this->attributes = $value;
        return $this;
    }

    /**
     * Возвращает список атрибутов
     *
     * @return array Массив атрибутов
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Устанавливает значение атрибута модели.
     *
     * @param string $name Имя атрибута
     * @param mixed $value Значение атрибута
     *
     * @return this
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * Возвращает значение атрибута модели.
     *
     * @param string $name Имя атрибута
     *
     * @return mixed Значение атрибута
     */
    public function getAttribute($name)
    {
        if ($this->hasAttribute($name)) {
            return $this->attributes[$name];
        }
        throw new \ErrorException("Undefined Model attribute $name");
    }

    /**
     * Определяет, существует ли атрибут с укзанным именем
     *
     * @param string $name Имя атрибута
     *
     * @return mixed Значение атрибута
     */
    public function hasAttribute($name)
    {
        return array_key_exists($name, $this->getAttributes());
    }

    /**
     * Магический метод для установки значений несуществующих или непубличных свойств.
     * Используется для установки значений атрибутов модели.
     *
     * @param string $name Имя атрибута
     * @param mixed $value Значение атрибута
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->setAttribute($name, $value);
    }

    /**
     * Магический метод для получения значений несуществующих или непубличных свойств.
     * Используется для получения значений атрибутов модели.
     *
     * @param string $name Имя атрибута
     *
     * @return mixed Значение атрибута
     */
    public function __get($name)
    {
        return $this->getAttribute($name);
    }

    /**
     * Устанавливает имя столбца с первичным ключом
     *
     * @param string $value Имя столбца с первичным ключом
     *
     * @return this
     */
    public function setPrimaryKeyName($value)
    {
        $this->primaryKeyName = $value;
        return $this;
    }

    /**
     * Возвращает имя столбца с первичным ключом
     *
     * @return string Имя столбца с первичным ключом
     */
    public function getPrimaryKeyName()
    {
        return $this->primaryKeyName;
    }

    /**
     * Устанавливает значение атрибута - первичного ключа
     *
     * @param string $value Значение атрибута - первичного ключа
     *
     * @return this
     */
    public function setPrimaryKeyValue($value)
    {
        return $this->setAttribute($this->getPrimaryKeyName(), $value);
    }

    /**
     * Возвращает значение атрибута - первичного ключа.
     * Если значение не определено, возвращает null.
     *
     * @return string Значение атрибута - первичного ключа
     */
    public function getPrimaryKeyValue()
    {
        $primaryKeyName = $this->getPrimaryKeyName();
        if (!$this->hasAttribute($primaryKeyName)) {
            return null;
        }
        return $this->getAttribute($primaryKeyName);
    }

    /**
     * Устанавливает имя атрибута "время создания".
     * Если задано имя этого атрибута, в таблице базы данных
     * фиксируется время создания новой записи.
     * Для этого в таблице должен присутствовать столбец
     * с таким же именем как у данного атрибута.
     *
     *
     * @param string $value Имя атрибута "время создания"
     *
     * @return this
     */
    public function setCreatedAtColName($value)
    {
        $this->createdAtColName = $value;
        return $this;
    }

    /**
     * Возвращает имя атрибута "время создания".
     * Если задано имя этого атрибута, в таблице базы данных
     * фиксируется время создания новой записи.
     * Для этого в таблице должен присутствовать столбец
     * с таким же именем как у данного атрибута.
     *
     * @return string Имя атрибута "время создания"
     */
    public function getCreatedAtColName()
    {
        return $this->createdAtColName;
    }

    /**
     * Устанавливает имя атрибута "время обновления".
     * Если задано имя этого атрибута, в таблице базы данных
     * фиксируется время последнего обновления записи.
     * Для этого в таблице должен присутствовать столбец
     * с таким же именем как у данного атрибута.
     *
     * @param string $value Имя атрибута "время обновления"
     *
     * @return this
     */
    public function setUpdatedAtColName($value)
    {
        $this->updatedAtColName = $value;
        return $this;
    }

    /**
     * Возвращает имя атрибута "время обновления".
     * Если задано имя этого атрибута, в таблице базы данных
     * фиксируется время последнего обновления записи.
     * Для этого в таблице должен присутствовать столбец
     * с таким же именем как у данного атрибута.
     *
     * @return string Имя атрибута "время обновления"
     */
    public function getUpdatedAtColName()
    {
        return $this->updatedAtColName;
    }

    /**
     * Устанавливает массив данных настройки атрибутов.
     * Формат массива:
     * [ имя_атрибута => экземпляр класса, реализующий интерфейс \App\Contracts\AttributeSettings ]
     *
     * @param array $value Массив данных настройки атрибутов.
     *
     * @return this
     */
    public function setAttributesSettings($value)
    {
        $this->attributesSettings = $value;
        return $this;
    }

    /**
     * Возвращает массив данных настройки атрибутов.
     * Формат массива:
     * [ имя_атрибута => экземпляр класса, реализующий интерфейс \App\Contracts\AttributeSettings ]
     *
     * @return array Массив данных настройки атрибутов.
     */
    public function getAttributesSettings()
    {
        return $this->attributesSettings;
    }

    /**
     * Задаёт цепочку проверки входных данных для указанного атрибута.
     *
     * @param array $name Имя атрибута
     * @param array $chain Цепочка проверки входных данных
     *
     * @return this
     */
    public function addAttributeChain($name, $chain)
    {
        $this->attributesSettings[$name] = attributeSettings()->
                setInputChain(inputChain()->setChain($chain));
    }

    /**
     * Устанавливает значения указанных атрибутов из
     * заданного источника входных данных.
     * Если указанный атрибут отсутствует в источнике данных,
     * предполагается, что присваемое ему значение = null.
     *
     * Если для устанавливаемых атрибутов назначена цепочка проверки,
     * она выполняется.
     * Устанавливается значение тех атрибутов, проверка которых прошла успешно.
     * Если все проверки прошли успешно, возвращается true
     * В случае возникновения ошибок, возвращается массив вида:
     * [ имя_атрибута => описание_ошибки ]
     *
     * @param string|array $names Имя атрибута или массив имён атрибутов.
     * @param array $source Источник данных, массив вида [ имя_атрибута => значение_атрибута ].
     *
     * @return bool|array Возвращает true в случае успеха или массив с описаниями ошибок.
     */
    public function input($names, $source)
    {
        $attributes = is_array($names) ? $names : [$names];
        $errors = [];
        foreach ($attributes as $attribute) {
            $attributeValue = isset($source[$attribute]) ? $source[$attribute] : null;
            $hasInputChain = isset($this->attributesSettings[$attribute]) &&
                    ($this->attributesSettings[$attribute]->getInputChain() !== null);

            // Если нет цепочки проверки
            if (!$hasInputChain) {
                $this->setAttribute($attribute, $attributeValue);
                continue;
            }

            // Если есть цепочка проверки
            $inputChain = $this->attributesSettings[$attribute]->getInputChain();
            $inputChain->setData($attributeValue);
            $result = $inputChain->handle();

            if ($result === true) {
                $this->setAttribute($attribute, $inputChain->getData());
            } else {
                $errors[$attribute] = $result;
            }
        }
        return empty($errors) ? true : $errors;
    }

}
