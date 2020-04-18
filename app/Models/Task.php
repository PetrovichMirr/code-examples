<?php

/**
 * @author Petrovich Mirr <vseti-24@mail.ru>
 */

namespace App\Models;

use App\Core\Model;
use App\Contracts\InputChain;

/**
 * Модель задачи
 *
 * @author petrovich
 */
class Task extends Model
{

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
        $this->setCreatedAtColName('created_at');
        $this->setUpdatedAtColName('updated_at');

        // Настраиваем атрибуты
        // Цепочки обработки входных данных
        $this->addAttributeChain('user_name', [InputChain::FILTER_TRIM, InputChain::RULE_NOT_EMPTY]);
        $this->addAttributeChain('email', [InputChain::FILTER_TRIM, InputChain::RULE_NOT_EMPTY, InputChain::RULE_EMAIL]);
        $this->addAttributeChain('content', [InputChain::FILTER_TRIM, InputChain::RULE_NOT_EMPTY]);
        $this->addAttributeChain('done', [InputChain::FILTER_TRIM, InputChain::FILTER_TO_BOOL_INT]);
    }

}
