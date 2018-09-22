<?php
namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Widget\Form\TEntry;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;

/**
 * Widget Form DEntry
 *
 * @version    Dvi 1.0
 * @package    form
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DEntry extends TEntry implements FormField
{
    use FormFieldTrait;
    use FormFieldValidation;
    use SearchableField;

    public function __construct(string $name, string $label = null, int $max_length = null, bool $required = false)
    {
        parent::__construct($name);

        $this->setup($label ?? $name, $required, $max_length);

        $this->operator('like');
    }
}
