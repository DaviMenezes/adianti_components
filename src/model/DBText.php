<?php

namespace Dvi\Adianti\Model;
use Dvi\Adianti\Widget\Form\DText;

/**
 * Model DBText
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Adianti
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DBText extends DBFormField
{
    public function __construct(string $name, string $label, int $length, int $height, bool $required = false)
    {
        parent::__construct($name, 'text', $required, $label);

        $this->field = new DText($name, $label, $length, $height, true, $required);
    }

    public static function create(string $name, int $length, int $height, bool $required = false, $label = null):DBText
    {
        return new DBText($name, $label, $length, $height, $required);
    }
}