<?php
namespace Dvi\Widget\Form\Field;

use Adianti\Widget\Form\TField;
use Dvi\Widget\IDviWidget;

/**
 * Model SearchableField
 *
 * @version    Dvi 1.0
 * @package    field
 * @subpackage form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
trait SearchableField
{
    private $search_operator;

    public function operator(string $operator)
    {
        $this->search_operator = $operator;
        return $this;
    }

    public function getSearchOperator()
    {
        return $this->search_operator;
    }

    public function getSearchableValue()
    {
        return $this->search_operator == 'like' ? "%{$this->getValue()}%" : $this->getValue();
    }


}