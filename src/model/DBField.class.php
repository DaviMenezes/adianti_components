<?php

namespace Dvi\Adianti\Model;

/**
 * Model DBField
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Adiant Components
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class DBField
{
    private $name;
    private $type;
    private $required;
    private $label;

    public function __construct(string $name, string $type, bool $required = false, string $label = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
        $this->label= $label;
    }

    protected function getName()
    {
        return $this->name;
    }

    protected function getType()
    {
        return $this->type;
    }

    protected function getRequired()
    {
        return $this->required;
    }

    protected function getLabel()
    {
        return $this->label;
    }

    protected abstract function getFormField();
}