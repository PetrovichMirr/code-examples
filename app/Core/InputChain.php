<?php


namespace App\Core;

use App\Contracts\InputChain as IInputChain;

/**
 * Проверка корректности и фильтрация значений.
 * Обработка данных выполняется согласно заданной
 * последовательности фильтров и правил валидации.
 * Если на каком-то этапе проверки возникла ошибка,
 * проверка прекращается и возвращается описание ошибки.
 *
 * Список правил представлен константами с именем RULE_*
 * Список фильтров представлен константами с именем FILTER_*
 *
 * @author petrovich
 */
class InputChain implements IInputChain
{

    // Данные для обработки цепочки
    const CHAIN_DATA = [
        self::CHAIN_ITEM_TYPE_RULES => [
            self::RULE_EMPTY => ['error' => 'Значение должно быть пустым', 'callback' => 'ruleEmpty'],
            self::RULE_NOT_EMPTY => ['error' => 'Значение не должно быть пустым', 'callback' => 'ruleNotEmpty'],
            self::RULE_EMAIL => ['error' => 'Значение должно быть корректным адресом электронной почты', 'callback' => 'filterInput', 'params' => [FILTER_VALIDATE_EMAIL]],
            self::RULE_INT => ['error' => 'Значение должно быть целым числом', 'callback' => 'filterInput', 'params' => [FILTER_VALIDATE_INT]],
        ],
        self::CHAIN_ITEM_TYPE_FILTERS => [
            self::FILTER_TRIM => ['error' => null, 'callback' => 'filterTrim'],
            self::FILTER_TO_BOOL => ['callback' => 'filterToBool'],
            self::FILTER_TO_BOOL_INT => ['callback' => 'filterToBoolInt'],
        ],
    ];

    /**
     * Цепочка правил и фильтров для обработки значений.
     * Формат массива: [ правило_или_фильтр, ... ]
     *
     * @var array
     */
    private $chain;

    /**
     * Обрабатываемые данные
     *
     * @var mixed
     */
    private $data;

    /**
     * Возвращает тип элемента цепочки: правило или фильтр
     *
     * @param string $chainItem Элемент цепочки.
     *
     * @return string Возвращает тип элемента цепочки: константы CHAIN_ITEM_TYPE_RULES (правила) или CHAIN_ITEM_TYPE_FILTER (фильтры).
     */
    private function getChainItemType($chainItem)
    {
        if (stripos($chainItem, 'rule_') === 0) {
            return self::CHAIN_ITEM_TYPE_RULES;
        }
        if (stripos($chainItem, 'filter_') === 0) {
            return self::CHAIN_ITEM_TYPE_FILTERS;
        }
    }

    /**
     * Устанавливает цепочку правил и фильтров для обработки.
     *
     * @param array $value Цепочка правил и фильтров для обработки.
     *
     * @return this
     */
    public function setChain($value)
    {
        $this->chain = $value;
        return $this;
    }

    /**
     * Возвращает цепочку правил и фильтров для обработки.
     *
     * @return array Цепочка правил и фильтров для обработки.
     */
    public function getChain()
    {
        return $this->chain;
    }

    /**
     * Устанавливает обрабатываемые данные.
     *
     * @param mixed $value Обрабатываемые данные.
     *
     * @return this
     */
    public function setData($value)
    {
        $this->data = $value;
        return $this;
    }

    /**
     * Возвращает обрабатываемые данные.
     *
     * @return mixed Обрабатываемые данные.
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Выполняет обработку данных.
     * Если обработка прошла успешно, возвращает true,
     * иначе возвращает строку с описанием ошибки.
     *
     * @return bool|string Если обработка прошла успешно, возвращает true, иначе возвращает строку с описанием ошибки
     */
    public function handle()
    {
        foreach ($this->getChain() as $chainItem) {
            $chainItemType = $this->getChainItemType($chainItem);

            $chainItemData = self::CHAIN_DATA[$chainItemType][$chainItem];
            $chainItemDataParams = isset($chainItemData['params']) ? $chainItemData['params'] : [];
            $params = array_merge([$this->data], $chainItemDataParams);
            // callback - функция должна возвращать массив  ['success' => true|false, 'result' => результат_обработки_данных ]
            $result = call_user_func_array([$this, $chainItemData['callback']], $params);
            if (!$result['success']) {
                return isset($chainItemData['error']) ? $chainItemData['error'] : '';
            }
            // Присваиваем обработанное значение данным
            $this->data = $result['result'];
        }
        return true;
    }

    /**
     * Проверяет или обрабатывает заданное значение.
     * Значение должно быть пустым.
     *
     * @param mixed $value Значение для проверки / обработки.
     *
     * @return array Результат обработки данных.
     */
    private function ruleEmpty($value)
    {
        return [
            'success' => empty($value),
            'result' => $value,
        ];
    }

    /**
     * Проверяет или обрабатывает заданное значение.
     * Значение не должно быть пустым.
     *
     * @param mixed $value Значение для проверки / обработки.
     *
     * @return array Результат обработки данных.
     */
    private function ruleNotEmpty($value)
    {
        return [
            'success' => !empty($value),
            'result' => $value,
        ];
    }

    /**
     * Проверяет или обрабатывает заданное значение.
     * Удаляет начальные и конечные пробельные символы.
     *
     * @param mixed $value Значение для проверки / обработки.
     *
     * @return array Результат обработки данных.
     */
    private function filterTrim($value)
    {
        return [
            'success' => true,
            'result' => trim($value),
        ];
    }

    /**
     * Преобразует значение в тип логический тип.
     *
     * @param mixed $value Значение для проверки / обработки.
     *
     * @return bool Результат преобразования в логический тип.
     */
    private function filterToBool($value)
    {
        return [
            'success' => true,
            'result' => $value == true,
        ];
    }

    /**
     * Преобразует значение в тип логический тип,
     * выраженный целым числом: 1 | 0.
     *
     * @param mixed $value Значение для проверки / обработки.
     *
     * @return bool Результат преобразования.
     */
    private function filterToBoolInt($value)
    {
        return [
            'success' => true,
            'result' => $value == true ? 1 : 0,
        ];
    }

    /**
     * Проверяет или обрабатывает заданное значение.
     * Вид обработки зависит от передаваемого параметра
     *
     * @param mixed $value Значение для проверки / обработки.
     * @param mixed $filter Идентификатор (ID) применяемого фильтра (см. PHP-функцию filter_var).
     *
     * @return array Результат обработки данных.
     */
    private function filterInput($value, $filter)
    {
        $filterResult = filter_var($value, $filter);
        return [
            'success' => $filterResult !== false,
            'result' => $filterResult,
        ];
    }

}
