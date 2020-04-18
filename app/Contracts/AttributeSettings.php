<?php

namespace App\Contracts;

/**
 * Настройки атрибута
 *
 * @author petrovich
 */
interface AttributeSettings
{

    /**
     * Устанавливает цепочку обработки входных данных.
     *
     * @param \App\Contracts\InputChain $value Цепочка обработки входных данных
     *
     * @return this
     */
    public function setInputChain($value);

    /**
     * Возвращает цепочку обработки входных данных.
     *
     * @return \App\Contracts\InputChain Цепочка обработки входных данных
     */
    public function getInputChain();
}
