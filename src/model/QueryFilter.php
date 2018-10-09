<?php

namespace Dvi\Adianti\Model;

/**
 * QueryFilter
 * Filtro para a queries uso em conjunto da classe ActiveRecord    model
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class QueryFilter
{
    public $field;
    public $operator;
    public $filter;
    public $value;
    public $value2;
    public $query_operator;
    
    public function __construct($field, $operator, $value = null, $value2 = null, $query_operator = 'AND')
    {
        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
        $this->value2 = $value2;
        $this->query_operator = $query_operator;
    }
}
