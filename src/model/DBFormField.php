<?php

namespace Dvi\Adianti\Model;

use Adianti\Base\Lib\Widget\Base\TElement;

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
    /**@var TElement $field*/
    protected $field;
    protected $type;
    private $label;

    public function __construct(string $name, string $type, bool $required = false, string $label = null)
    {
        parent::__construct($name, $type, $required);

        $this->label= $label ?? $name;
    }

    public function getLabel()
    {
        return str_replace('_', ' ', $this->label);
    }

    abstract public function getField();
}
