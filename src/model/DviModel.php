<?php

namespace Dvi\Adianti\Model;

use Dvi\Adianti\Component\Model\Form\Fields\FieldCombo;
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
    private $form_rows = array();

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
        $this->$field->mask('A!');

        return $this->$field;
    }

    protected function addText(string $name, int $length, int $height, bool $required, string $label):FieldText
    {
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

    protected function addCombo(string $name, string $model, string $value, string $label = null, bool $required = false):FieldCombo
    {
        $field = 'field_'.$name;
        $this->$field = new FieldCombo($name, $model, $value, 'combo', $required, $label);

        return $this->$field;
    }
    #endregion

    #region[BUILDING FIELDS]
    protected function setStructureForm(array $form_column_structure)
    {
        $this->form_rows = $form_column_structure;
    }

    private function setFormStructureColumn()
    {
        foreach ($this->form_rows as $key => $row) {
            $cols = array();
            foreach ($row as $column) {
                if (empty($column)) {
                    throw new \Exception('Verifique o nome do campo ' . ($key +1));
                }

                $fc = mb_strtoupper(mb_substr($column->getLabel(), 0, 1));
                $label = $fc.mb_substr($column->getLabel(), 1);
                $field = $column->getFormField();

                $dvbox = new DVBox();
                $dvbox->add($label);
                $dvbox->add($field);
                $cols[] = new DGridColumn($dvbox);
            }
            $this->form_rows[$key] = $cols;
        }
    }

    public function getFormRows()
    {
        $this->buildFieldTypes();
        $this->buildStructureForm();

        $this->setFormStructureColumn();

        return $this->form_rows;
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
    abstract protected function buildFieldTypes();

    abstract protected function buildStructureForm();
    #endregion
}
