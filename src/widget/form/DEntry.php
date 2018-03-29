<?php
namespace Dvi\Adianti\Widget\Form;

use Adianti\Base\Lib\Validator\TEmailValidator;
use Dvi\Adianti\Widget\Form\Field\FieldEntry;
use Dvi\Adianti\Widget\Form\Field\SearchableField;

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
class DEntry extends FieldEntry
{
    use SearchableField;

    public function __construct(
        string $name,
        string $placeholder = null,
        int $max_length = null,
        bool $required = false,
        bool $tip = true
    ) {
        parent::__construct($name, $placeholder, $max_length, $required, $tip);

        $this->operator('like');
    }

    public static function create(
        string $name,
        string $placeholder = null,
        int $max_length = null,
        bool $required = false,
        bool $tip = true
    ):DEntry {
        $obj = new DEntry($name, $placeholder, $max_length, $required, $tip);

        return $obj;
    }

    public function validate()
    {
        if ($this->getValidations()) {
            foreach ($this->getValidations() as $validation) {
                $label      = $validation[0];
                $validator  = $validation[1];
                $parameters = $validation[2];

                if ($validator instanceof TEmailValidator and empty($this->value)) {
                    continue;
                }
                $validator->validate($label, $this->getValue(), $parameters);
            }
        }
    }
}
