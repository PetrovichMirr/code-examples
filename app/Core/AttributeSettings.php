<?php

namespace App\Core;

use App\Contracts\AttributeSettings as IAttributeSettings;

/**
 * Настройки атрибута
 *
 * @author petrovich
 */
class AttributeSettings implements IAttributeSettings
{

    /**
     * Цепочка обработки входных данных.
     *
     * @var \App\Contracts\InputChain
     */
    private $inputChain;

    /**
     * Устанавливает цепочку обработки входных данных.
     *
     * @param \App\Contracts\InputChain $value Цепочка обработки входных данных
     *
     * @return this
     */
    public function setInputChain($value)
    {
        $this->inputChain = $value;
        return $this;
    }

    /**
     * Возвращает цепочку обработки входных данных.
     *
     * @return \App\Contracts\InputChain Цепочка обработки входных данных
     */
    public function getInputChain()
    {
        return $this->inputChain;
    }

}
