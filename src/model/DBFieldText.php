<?php

namespace Dvi\Adianti\Model;

use Dvi\Adianti\Widget\Form\DEntry;

/**
 * Model DBField
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Adianti Component
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DBFieldText extends DBField
{
    private $size;

    public function __construct(string $name, string $type, int $size, bool $required = false, $label = null)
    {
        parent::__construct($name, $type, $required, $label);

        $this->size = $size;
    }

    public function getSize()
    {
        return $this->size;
    }

    function getFormField()
    {
        $dentry = new DEntry($this->getName(), $this->getLabel(), $this->getSize(), $this->getRequired());
        return $dentry;
    }
}