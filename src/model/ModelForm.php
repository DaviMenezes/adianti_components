<?php

namespace Dvi\Adianti\Model;

use Dvi\Adianti\Widget\Base\DGridColumn;
use Dvi\Adianti\Widget\Container\DVBox;
use Dvi\Adianti\Widget\Form\DBCombo;

/**
 * Model ModelForm
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Adianti Components
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait ModelForm
{
    private static $attributes_field;

    private $form_rows = array();

    private function addVarchar(string $name, int $size, bool $required = false, $label = null):DBVarchar
    {
        parent::addAttribute($name);

        $field = 'field_'.$name;

        $this->$field = DBVarchar::create($name, 'text', $size, $required, $label);
        $this->$field->mask('A!');

        return $this->$field;
    }

    private function addText(string $name, int $length, int $height, bool $required, string $label):DBText
    {
        parent::addAttribute($name);

        $field = 'field_'.$name;
        $this->$field = DBText::create($name, $length, $height, $required, $label);

        return $this->$field;
    }

    private function addDateTime(string $name, $label = null, bool $required = false):DBDateTime
    {
        parent::addAttribute($name);

        $field = 'field_'.$name;
        $this->$field = DBDateTime::create($name, $required, $label);

        return $this->$field;
    }

    private function addCombo(string $name, string $model, string $value, string $label = null, bool $required = false):DBCombo
    {
        $field = 'field_'.$name;
        $this->$field = new DBCombo($name, $model, $value, 'combo', $required, $label);

        return $this->$field;
    }

    private function setStructureForm(array $form_column_structure)
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
}
