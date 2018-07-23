<?php
namespace Dvi\Adianti\Widget\Form;

use Adianti\Base\Lib\Widget\Form\TEntry;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;
use Dvi\Adianti\Widget\Form\Field\FormFieldValidation;
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
class DEntry extends TEntry implements FormField
{
    use FormFieldTrait;
    use FormFieldValidation;
    use SearchableField;

    public function __construct(
        string $name,
        string $placeholder = null,
        int $max_length = null,
        bool $required = false,
        bool $tip = true
    ) {
        parent::__construct($name);

        $this->prepare($placeholder, $required, $tip, $max_length);

        $this->operator('like');
    }

//    public function validate()
//    {
//        if ($this->getValidations()) {
//            foreach ($this->getValidations() as $validation) {
//                $label      = $validation[0];
//                $validator  = $validation[1];
//                $parameters = $validation[2];
//
//                if ($validator instanceof RequiredValidator) {
//                    continue;
//                }
//                if ($validator instanceof TEmailValidator and empty($this->value)) {
//                    continue;
//                }
//                /**@var FieldValidator $validator*/
//                if ($validator->validate($label, $this->getValue(), $parameters)) {
//                    $this->addErrorMessage($validator->getErrorMsg());
//                }
//            }
//        }
//    }

}
