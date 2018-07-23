<?php

namespace Dvi\Adianti\View\Standard;

/**
 * Control GroupFieldView
 *
 * @package    Control
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class GroupFieldView
{
    protected $fields = array();
    protected $tab;

    public function tab($name)
    {
        $this->tab = $name;
    }

    public function fields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    public function hasTab():bool
    {
        if (!empty($this->tab)) {
            return true;
        }
        return false;
    }

    public function getTab()
    {
        return $this->tab;
    }

    public function getFields()
    {
        return $this->fields;
    }
}
