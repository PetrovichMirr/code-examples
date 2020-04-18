<?php

namespace App\Core;

/**
 * Фабрика объектов, статический класс
 *
 * @author petrovich
 */
class Factory
{

    /**
     * Список экземпляров объектов, создаваемых только один раз (объекты-одиночки).
     * Формат списка: [ интерфейс => экземпляр_объекта, ... ]
     *
     * @var array
     */
    private static $singleInstances = [];

    /**
     * Создаёт экземпляр объекта по имени класса.
     *
     * @param string $class Имя класса
     *
     * @return mixed Экземпляр класса.
     */
    private static function create(string $class)
    {
        return new $class();
    }

    /**
     * Возвращает экземпляр класса, реализующего указанный интерфейс.
     * Если интерфейс не зарегистрирован в конфигурации, возвращает null.
     *
     * @param string $interface Интерфейс
     *
     * @return mixed Экземпляр класса, реализующий указанный интерфейс.
     */
    public static function make(string $interface)
    {
        $appCoreAliases = APP_CORE_ALIASES;
        // Проверяем регистрацию интерфейса
        if (!isset($appCoreAliases[$interface])) {
            return null;
        }

        // Если объект - одиночка и уже есть ранее созданный экземпляр,
        // возвращаем его, иначе создаём новый объект.
        // Если новый созданный объект - одиночка, добавляем его в список ранее созданных экземпляров.
        if ($appCoreAliases[$interface]['singleton'] && isset(self::$singleInstances[$interface])) {
            return self::$singleInstances[$interface];
        } else {
            $instance = self::create($appCoreAliases[$interface]['implements']);
        }
        if ($appCoreAliases[$interface]['singleton']) {
            self::$singleInstances[$interface] = $instance;
        }
        return $instance;
    }

}
