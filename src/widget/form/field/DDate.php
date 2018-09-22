<?php
namespace Dvi\Adianti\Widget\Form\Field;

use Adianti\Base\App\Lib\Validator\TDateValidator;
use Adianti\Base\Lib\Widget\Form\TDate;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\Form\Field\FormField as FormFieldTrait;

/**
 * Model DDate
 *
 * @version    Dvi 1.0
 * @package    form
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DDate extends TDate implements FormField
{
    use FormFieldTrait;
    use FormFieldValidation;
    use SearchableField;

    private $field_disabled;

    public function __construct($name, string $label = null, bool $required = false)
    {
        parent::__construct($name);

        $this->setup($label ?? $name, $required);
        $this->setMask('dd/mm/yyyy');
        $this->setDatabaseMask('yyyy-mm-dd');

        $this->operator('=');

        $this->addValidation($this->getLabel(), new TDateValidator());
    }

    public function disable($disable = true)
    {
        $this->field_disabled = $disable;

        parent::setEditable(!$disable);
    }

    public function isDisabled()
    {
        return $this->field_disabled;
    }
}
