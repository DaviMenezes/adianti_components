<?php

namespace Dvi\Adianti\Widget\Form;

use Adianti\Base\Lib\Widget\Form\THidden;

/**
 * Form DHidden
 *
 * @version    Dvi 1.0
 * @package    Form
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DHidden extends THidden
{
    public function __construct(string $name, $default_value = null)
    {
        parent::__construct($name);

        if ($default_value) {
            $this->setValue($default_value);
        }
    }
}
