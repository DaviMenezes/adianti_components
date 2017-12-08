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
    private $field;

    public function __construct(string $name, string $type, int $size, bool $required = false, $label = null)
    {
        parent::__construct($name, $type, $required, $label);
        
        $this->size = $size;

        $this->field = new DEntry($name, $label, $size, $required);
    }

    protected function getFormField()
    {
        return $this->field;
    }
}
