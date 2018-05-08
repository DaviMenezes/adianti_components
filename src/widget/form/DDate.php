<?php
namespace Dvi\Adianti\Widget\Form;

use Adianti\Base\Lib\Validator\TRequiredValidator;
use Adianti\Base\Lib\Widget\Form\TDate;
use Dvi\Adianti\Widget\Form\Field\DField;
use Dvi\Adianti\Widget\Form\Field\SearchableField;

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
class DDate extends TDate
{
    use SearchableField;
    use DField;

    private $field_disabled;

    public function __construct($name, string $placeholder = null, bool $required = false, bool $tip = true)
    {
        parent::__construct($name);

        $this->setLabel($placeholder);

        if ($placeholder) {
            $this->placeholder = $placeholder;
        }

        if ($required) {
            $this->addValidation(ucfirst($placeholder), new TRequiredValidator());
        }

        if ($tip) {
            $this->setTip(ucfirst($this->placeholder));
        }

        //Todo check location user
        $this->setMask('dd/mm/yyyy');
        $this->setDatabaseMask('yyyy-mm-dd');

        $this->operator('=');
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
