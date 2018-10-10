<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Widget\Form\TPassword;

/**
 * Form Password
 *
 * @package    Form
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class Password extends TPassword
{
    use FormField;

    public function __construct(string $name, int $max_length, string $label = null, bool $required = false)
    {
        parent::__construct($name);

        $this->setup($label ?? $name, $required, $max_length);
    }
}
