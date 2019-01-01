<?php

namespace Dvi\Adianti\View\Standard;

/**
 * Control GroupFieldView
 * This class organizes the fields into groups with tabs. Or not.
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
        return $this;
    }

    public function fields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    public function hasTab(): bool
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
