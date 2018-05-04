<?php

namespace Dvi\Adianti\Widget\Form;

use Adianti\Base\Lib\Widget\Form\TText;
use Dvi\Adianti\Widget\Form\Field\DField;

/**
 * Model DText
 *
 * @version    Dvi 1.0
 * @package    form
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DText extends TText
{
    private $field_disabled;

    use DField;

    public function __construct(string $name, string $placeholder = null, int $max_length = null, $height = '50', bool $tip = true, bool $required = false)
    {
        parent::__construct($name);

        $this->prepare($this->placeholder, $required, $tip, $max_length);

        $this->setSize(0, $height);
    }

    public function setMaxLength(int $length)
    {
        if ($length > 0) {
            $this->tag->maxlength = $length;
        }
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
