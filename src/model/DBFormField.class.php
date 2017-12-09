<?php

namespace Dvi\Adianti\Model;

/**
 * Model DBFormField
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Adianti
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class DBFormField extends DBField
{
    protected $field;
    private $label;

    public function __construct(string $name, string $type, bool $required = false, string $label = null)
    {
        parent::__construct($name, $type, $required);

        $this->label= $label;
    }

    public function getLabel()
    {
        return ucfirst($this->label);
    }

    abstract public function setMask(string $mask);

    public function getFormField()
    {
        return $this->field;
    }
}
