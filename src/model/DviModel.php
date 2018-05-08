<?php

namespace Dvi\Adianti\Model;

use App\Adianti\Component\Model\Form\Fields\FieldInteger;
use Dvi\Adianti\Component\Model\Form\Fields\FieldCombo;
use Dvi\Adianti\Component\Model\Form\Fields\FieldCurrency;
use Dvi\Adianti\Component\Model\Form\Fields\FieldDate;
use Dvi\Adianti\Component\Model\Form\Fields\FieldDateTime;
use Dvi\Adianti\Component\Model\Form\Fields\FieldHtml;
use Dvi\Adianti\Component\Model\Form\Fields\FieldRadio;
use Dvi\Adianti\Component\Model\Form\Fields\FieldText;
use Dvi\Adianti\Component\Model\Form\Fields\FieldVarchar;
use Dvi\Adianti\Widget\Base\DGridColumn;

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

        $field_name = 'field_'.$name;
        $table_field_name = $this->getTableFieldName($name);
        $field_varchar = FieldVarchar::create($table_field_name, 'text', $size, $required, $label);
        $field_varchar->setType('string');
        $this->$field_name = $field_varchar;

        return $this->$field_name;
    }

    protected function addRadioGroup(string $name, bool $required = false, $label = null):FieldRadio
    {
        parent::addAttribute($name);

        $field_name = 'field_'.$name;
        $table_field_name = $this->getTableFieldName($name);
        $field = new FieldRadio($table_field_name, 'string', $required, $label);
        $field->setType('string');
        $this->$field_name = $field;
        return $this->$field_name;
    }

    protected function addCurrency(
        $name,
        $decimals,
        $decimalsSeparator,
        $thousandSeparator,
        $required = false,
        $label = null
    ):FieldCurrency {
        parent::addAttribute($name);

        $field_name = 'field_'.$name;
        $table_field_name = $this->getTableFieldName($name);

        $field = new FieldCurrency(
            $table_field_name,
            $decimals,
            $decimalsSeparator,
            $thousandSeparator,
            'currency',
            $required,
            $label
        );

        $field->setType('string');

        $this->$field_name = $field;

        return $this->$field_name;
    }

    protected function addText(
        string $name,
        int $maxlength,
        int $height,
        bool $required = false,
        string $label = null
    ):FieldText {
        parent::addAttribute($name);

        $field_name = 'field_'.$name;

        $table_field_name = $this->getTableFieldName($name);

        $field = new FieldText($table_field_name, $maxlength, $height, $required, $label);

        $field->setType('string');

        $this->$field_name = $field;

        return $this->$field_name;
    }

    protected function addHtml(
        string $name,
        int $maxlength,
        int $height,
        bool $required = false,
        string $label = null
    ):FieldHtml {
        parent::addAttribute($name);

        $field_name = 'field_'.$name;

        $table_field_name = $this->getTableFieldName($name);

        $field = new FieldHtml($table_field_name, $maxlength, $height, $required, $label);

        $field->setType('string');

        $this->$field_name = $field;

        return $this->$field_name;
    }

    protected function addDate(string $name, $label = null, bool $required = false):FieldDate
    {
        parent::addAttribute($name);

        $field_name = 'field_'.$name;

        $table_field_name = $this->getTableFieldName($name);

        $field = new FieldDate($table_field_name, $required, $label);

        $field->setType('string');

        $this->$field_name = $field;

        return $this->$field_name;
    }

    protected function addDateTime(string $name, $label = null, bool $required = false):FieldDateTime
    {
        parent::addAttribute($name);

        $field_name = 'field_'.$name;

        $table_field_name = $this->getTableFieldName($name);
        $field = new FieldDateTime($table_field_name, $required, $label);

        $field->setType('string');

        $this->$field_name = $field;

        return $this->$field_name;
    }

    protected function addCombo(string $name, string $label = null, bool $required = false):FieldCombo
    {
        parent::addAttribute($name);

        $field_name = 'field_'.$name;

        $table_field_name = $this->getTableFieldName($name);
        $field = new FieldCombo($table_field_name, 'combo', $required, $label);

        $field->setType('string');

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

        $table_field_name = $this->getTableFieldName($name);

        $field = new FieldInteger($table_field_name, $min, $max, $step, $required, $label);

        $field->setType('string');

        $this->$field_name = $field;

        return $this->$field_name;
    }

    protected function getTableFieldName(string $name): string
    {
        $table_field_name = (new \ReflectionClass(get_called_class()))->getShortName() . '_' . $name;
        return $table_field_name;
    }
    #endregion

    #region[BUILDING FIELDS]
    public function setStructureForm(array $form_column_structure)
    {
        $this->form_row_fields = $form_column_structure;
    }

    public function getStructureFields()
    {
        return $this->form_row_fields;
    }

    private function setFormStructureColumn()
    {
        foreach ($this->form_row_fields as $key => $form_row_field) {
            $cols = array();
            foreach ($form_row_field as $row_column_key => $row_column_value) {
                $class = null;
                if (is_array($row_column_value)) {
                    $column_value = $row_column_value[0];
                    $class = $row_column_value[1];
                } else {
                    $column_value = $row_column_value;
                }
                if (empty($column_value)) {
                    throw new \Exception('Verifique o nome dos campos');
                }

                $field = $column_value->getField();

                $cols[] = new DGridColumn($field, $class);
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

    public function setMap($attribute_name, $class)
    {
        $this->foreign_keys[$attribute_name] = $class;
        $this->addAttribute((string)$attribute_name.'_id');

        if (empty($this->id)) {
            $obj = new $class();
            return $obj;
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

    public function getAttributes()
    {
        if (count($this->attributes)) {
            return $this->attributes;
        }

        $this->buildFieldTypes();
        $attributes = parent::getAttributes();

        return $attributes;
    }
}
