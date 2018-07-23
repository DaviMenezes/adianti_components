<?php

namespace Dvi\Adianti\Widget\Form;

use Adianti\Base\Lib\Widget\Form\TPassword;
use Dvi\Adianti\Widget\Form\Field\FormField;

/**
 * Form DPassword
 *
 * @package    Form
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DPassword extends TPassword
{
    use FormField;

    public function __construct(string $name, string $placeholder = null, bool $required = false, bool $tip = true)
    {
        parent::__construct($name);

        $this->prepare($placeholder, $required, $tip);
    }
}
