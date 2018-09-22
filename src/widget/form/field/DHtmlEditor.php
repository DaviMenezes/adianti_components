<?php

namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\Lib\Widget\Form\THtmlEditor;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;
use Dvi\Adianti\Widget\Form\Field\FormFieldValidation;
use Dvi\Adianti\Widget\Form\Field\SearchableField;
use Dvi\Lib\Widget\Base\DScript;

/**
 * Form DHtmlEditor
 *
 * @package    Form
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DHtmlEditor extends THtmlEditor implements FormField
{
    use FormFieldTrait;
    use FormFieldValidation;
    use SearchableField;

    public function __construct(string $name, int $height, $label, bool $required = false)
    {
        parent::__construct($name);

        $this->setup($label, $required);

        $this->setSize('100%', $height);

        DScript::add('remove_note-popover', '$(".note-popover").remove();');
    }
}
