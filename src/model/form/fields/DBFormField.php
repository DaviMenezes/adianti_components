<?php

namespace Dvi\Adianti\Model\Fields;

use Dvi\Adianti\Widget\Form\Field\Contract\FormField as IFormField;

/**
 * Model DBFormField
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Adianti
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class DBFormField
{
    /**@var IFormField $field*/
    protected $field;
    protected $label;
    protected $required;
    protected $hide_in_edit;

    public function __construct(bool $required = false, string $label = null)
    {
        $this->label= $label;
        $this->required = $required;
    }

    public function getLabel()
    {
        return str_replace('_', ' ', $this->label);
    }

    abstract public function getField();

    public function getRequired()
    {
        return $this->required;
    }

    public function hideInEdit()
    {
        $this->hide_in_edit = true;
        return $this;
    }

    public function getHideInEdit()
    {
        return $this->hide_in_edit;
    }
}
