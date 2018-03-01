<?php

namespace Dvi\Adianti\Model;

use App\Adianti\Component\Model\Form\Fields\FieldInteger;
use Dvi\Adianti\Component\Model\Form\Fields\FieldCombo;
use Dvi\Adianti\Component\Model\Form\Fields\FieldCurrency;
use Dvi\Adianti\Component\Model\Form\Fields\FieldDate;
use Dvi\Adianti\Component\Model\Form\Fields\FieldDateTime;
use Dvi\Adianti\Component\Model\Form\Fields\FieldText;
use Dvi\Adianti\Component\Model\Form\Fields\FieldVarchar;
use Dvi\Adianti\Widget\Base\DGridColumn;
use Dvi\Adianti\Widget\Container\DVBox;

/**
 * Model DviModel
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Components
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class DviModel extends DviTRecord
{
    private $form_row_fields = array();

    public function __construct($id = null, bool $callObjectLoad = true)
    {
        parent::__construct($id, $callObjectLoad);
    }

    #region[FIELDS]
    protected function addVarchar(string $name, int $size, bool $required = false, $label = null):FieldVarchar
    {
        parent::addAttribute($name);

        $field = 'field_'.$name;

        $this->$field = FieldVarchar::create($name, 'text', $size, $required, $label);

        return $this->$field;
    }

    protected function addCurrency(
        $name,
        $decimals,
        $decimalsSeparator,
        $thousandSeparator,
        $required,
        $label
    ):FieldCurrency {
        parent::addAttribute($name);

        $field = 'field_'.$name;

        $this->$field = new FieldCurrency(
            $name,
            $decimals,
            $decimalsSeparator,
            $thousandSeparator,
            'currency',
            $required,
            $label
        );

        return $this->$field;
    }

    protected function addText(
        string $name,
        int $length,
        int $height,
        bool $required = false,
        string $label = null
    ):FieldText {
        parent::addAttribute($name);

        $field = 'field_'.$name;
        $this->$field = FieldText::create($name, $length, $height, $required, $label);

        return $this->$field;
    }

    protected function addDate(string $name, $label = null, bool $required = false):FieldDate
    {
        parent::addAttribute($name);

        $field = 'field_'.$name;
        $this->$field = FieldDate::create($name, $required, $label);

        return $this->$field;
    }

    protected function addDateTime(string $name, $label = null, bool $required = false):FieldDateTime
    {
        parent::addAttribute($name);

        $field = 'field_'.$name;
        $this->$field = FieldDateTime::create($name, $required, $label);

        return $this->$field;
    }

    protected function addCombo(string $name, string $label = null, bool $required = false):FieldCombo
    {
        parent::addAttribute($name);

        $field_name = 'field_'.$name;
        $field = new FieldCombo($name, 'combo', $required, $label);
        $this->$field_name = $field;

        return $this->$field_name;
    }

    protected function addInteger(
        string $name,
        int $min,
        int $max,
        int $step,
        string $label,
        $required = false
    ):FieldInteger {
        parent::addAttribute($name);

        $field_name = 'field_'.$name;
        $field = new FieldInteger($name, $min, $max, $step, $required, $label);
        $this->$field_name = $field;

        return $this->$field_name;
    }
    #endregion

    #region[BUILDING FIELDS]
    protected function setStructureForm(array $form_column_structure)
    {
        $this->form_row_fields = $form_column_structure;
    }

    private function setFormStructureColumn()
    {
        foreach ($this->form_row_fields as $key => $form_row_field) {
            $cols = array();
            foreach ($form_row_field as $row_column_key => $row_column_value) {
                if (empty($row_column_value)) {
                    throw new \Exception('Verifique o nome dos campos');
                }

                $fc = mb_strtoupper(mb_substr($row_column_value->getLabel(), 0, 1));
                $label = $fc.mb_substr($row_column_value->getLabel(), 1);
                $field = $row_column_value->getField();

                $dvbox = new DVBox();
                $dvbox->add($label);
                $dvbox->add($field);
                $cols[] = new DGridColumn($dvbox);
            }
            $this->form_row_fields[$key] = $cols;
        }
    }

    public function getFormRowFields()
    {
        $this->buildFieldTypes();
        $this->buildStructureForm();

        $this->setFormStructureColumn();

        return $this->form_row_fields;
    }

    public function setMap(string $atribute, $class)
    {
        $this->foreign_keys[$atribute] = $class;
        $this->addAttribute($atribute.'_id');

        if (empty($this->id)) {
            $this->$atribute = new $class;
        }
    }

    public function build()
    {
        $this->buildFieldTypes();
        $this->buildStructureForm();

        return $this;
    }
    #endregion

    #region[ABSTRACT METHODS]
    abstract public function buildFieldTypes();

    abstract protected function buildStructureForm();
    #endregion
}
