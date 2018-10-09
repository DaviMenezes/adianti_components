<?php
namespace Dvi\Adianti\Widget\Form\Field;

/**
 *  SearchableField
 * @package    field
 * @subpackage form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait SearchableField
{
    protected $search_operator;

    public function operator(string $operator)
    {
        $this->search_operator = $operator;
        return $this;
    }

    public function getSearchOperator()
    {
        return $this->search_operator = $this->search_operator ?? '=';
    }

    public function getSearchableValue()
    {
        return $this->search_operator == 'like' ? "%{$this->getValue()}%" : $this->getValue();
    }
}
