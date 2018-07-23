<?php

namespace Dvi\Adianti\Widget\Form\Field\Contract;

/**
 * Field SearchableFieldInterface
 *
 * @package    Field
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
interface SearchableField
{
    public function operator(string $operator);
    public function getSearchOperator();
    public function getSearchableValue();
}
