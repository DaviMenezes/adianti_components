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
class DBField
{
    private $name;
    private $type;
    private $required;


    public function __construct(string $name, string $type, bool $required = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getRequired()
    {
        return $this->required;
    }



//    protected abstract function getFormField();
}