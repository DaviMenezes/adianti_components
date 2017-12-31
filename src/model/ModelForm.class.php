<?php

namespace Dvi\Adianti\Model;

use Dvi\Adianti\Widget\Base\DGridColumn;
use Dvi\Adianti\Widget\Container\DVBox;
use Dvi\Adianti\Widget\Form\DEntry;
use Dvi\Adianti\Widget\Form\DviPanelGroup;
use FontLib\Table\Type\name;

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
        $this->$name = DBVarchar::create($name, 'text', $size, $required, $label);
        $this->$name->mask('A!');

        return $this->buildField($name);
    }

    private function addText(string $name, int $length, int $height, bool $required, string $label):DBText
    {
        $this->$name = DBText::create($name, $length, $height, $required, $label);

        return $this->buildField($name);
    }

    private function addDateTime(string $name, $label = null, bool $required = false):DBDateTime
    {
        $this->$name = DBDateTime::create($name, $required, $label);

        return $this->buildField($name);
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
                /**@var DBVarchar $column*/
                $label = ucfirst($column->getLabel());
                $field = $column->getFormField();

                $dvbox = new DVBox();
                $dvbox->style = 'width: 100%';
                $dvbox->add($label);
                $dvbox->add($field);
                $cols[] = new DGridColumn($dvbox);
            }
            $this->form_rows[$key] = $cols;
        }
    }

    public function getFormRows()
    {
        $this->setFormStructureColumn();

        return $this->form_rows;
    }

    private function buildField(string $name)
    {
        parent::addAttribute($name);

        return $this->$name;
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
