<?php
namespace Dvi\Widget\Form\Field;

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
    private $is_searchable = true; // estender uma classe com essa propriedade setada
    public $search_operator = 'like';

    public function getSearchableValue()
    {
        return $this->search_operator == 'like' ? "%{$this->getValue()}%" : $this->getValue();
    }
}