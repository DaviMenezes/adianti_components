<?php

namespace Dvi\Adianti\Widget\Base;

use Dvi\Adianti\Widget\IGroupField;

/**
 * Component GroupField
 *
 * @version    Dvi 1.0
 * @package    Component
 * @subpackage Bootstrap
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class GroupField implements IGroupField
{
    protected $childs = array();

    protected function addChilds($field)
    {
        $this->childs[] = $field;
    }

    public function getChilds(): array
    {
        return $this->childs;
    }
}
