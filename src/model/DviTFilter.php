<?php

namespace Dvi\Adianti\Model;

/**
 * Description of DviTFilter
 *
 * Filtro para a queries uso em conjunto da classe DviTRecord
 *
 * @version    Dvi 1.0
 * @package    model
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DviTFilter
{
    public $field;
    public $operator;
    public $filter;
    public $value;
    public $value2;
    public $query_operator;
    
    public function __construct($field, $operator, $filter = null, $value = null, $value2 = null, $query_operator = 'AND')
    {
        $this->field = $field;
        $this->operator = $operator;
        $this->filter = $filter;
        $this->value = $value;
        $this->value2 = $value2;
        $this->query_operator = $query_operator;
    }
}
