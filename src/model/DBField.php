<?php

namespace Dvi\Adianti\Model;

/**
 * DBField
 * @package    Model
 * @subpackage Adiant Components
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class DBField
{
    protected $name;
    protected $required;
    protected $hide_in_edit;

    public function __construct(string $name, bool $required = false)
    {
        $this->name = $name;
        $this->required = $required;
    }

    public function getRequired()
    {
        return $this->required;
    }

    public function hideInEdit()
    {
        $this->hide_in_edit = true;
        return $this;
    }

    public function getHideInEdit()
    {
        return $this->hide_in_edit;
    }
}
