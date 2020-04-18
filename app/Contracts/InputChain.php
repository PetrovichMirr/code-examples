<?php

namespace App\Contracts;

/**
 * Цепочка проверок входных параметров
 *
 * @author petrovich
 */
interface InputChain
{

    // Тип элемента цепочки: правила
    const CHAIN_ITEM_TYPE_RULES = 'rules';
    //
    // Тип элемента цепочки: фильтры
    const CHAIN_ITEM_TYPE_FILTERS = 'filters';
    //
    //
    //
    // Список правил
    //
    // Значение должно быть пустым:
    // ("" (пустая строка)
    // 0 (целое число)
    // 0.0 (число с плавающей точкой)
    // "0" (строка)
    // NULL
    // FALSE
    // [] (пустой массив)
    const RULE_EMPTY = 'rule_empty';
    //
    // Значение НЕ должно быть пустым, т.е. Не должно быть:
    // ("" (пустая строка)
    // 0 (целое число)
    // 0.0 (число с плавающей точкой)
    // "0" (строка)
    // NULL
    // FALSE
    // [] (пустой массив)
    const RULE_NOT_EMPTY = 'rule_not_empty';
    //
    // Значение должно быть валидным адресом электронной почты.
    const RULE_EMAIL = 'rule_email';
    //
    // Значение должно быть целым числом.
    const RULE_INT = 'rule_int';
    //
    //
    // Список фильтров
    //
    // Удаление начальных и конечных пробельных символов.
    const FILTER_TRIM = 'filter_trim';
    //
    // Преобразование в логический тип bool.
    const FILTER_TO_BOOL = 'filter_to_bool';
    //
    // Преобразование в логический тип, выраженный целым числом: 0 | 1.
    const FILTER_TO_BOOL_INT = 'filter_to_bool_int';

    /**
     * Устанавливает цепочку правил и фильтров для обработки.
     *
     * @param array $value Цепочка правил и фильтров для обработки.
     *
     * @return this
     */
    public function setChain($value);

    /**
     * Возвращает цепочку правил и фильтров для обработки.
     *
     * @return array Цепочка правил и фильтров для обработки.
     */
    public function getChain();

    /**
     * Устанавливает обрабатываемые данные.
     *
     * @param mixed $value Обрабатываемые данные.
     *
     * @return this
     */
    public function setData($value);

    /**
     * Возвращает обрабатываемые данные.
     *
     * @return mixed Обрабатываемые данные.
     */
    public function getData();

    /**
     * Выполняет обработку данных.
     * Если обработка прошла успешно, возвращает true,
     * иначе возвращает строку с описанием ошибки.
     *
     * @return bool|string Если обработка прошла успешно, возвращает true, иначе возвращает строку с описанием ошибки
     */
    public function handle();
}
