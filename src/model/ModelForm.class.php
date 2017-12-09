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

    private function setTypeText(string $name, int $size, bool $required = false, $label = null)
    {
        $model = get_called_class();
        $this->$name = new DBFieldText($name, 'text', $size, $required, $label);
    }

    private function addVarchar(string $name, int $size, bool $required = false, $label = null):DBVarchar
    {
        $field = DBVarchar::create($name, 'text', $size, $required, $label);
        $field->setMask('A!');

        return $this->buildField($name, $field);
    }

    private function addDateTime(string $name, $label = null, bool $required = false):DBDateTime
    {
        $field = DBDateTime::create($name, $required, $label);

        return $this->buildField($name, $field);
    }

    private function setStructureForm(array $rows)
    {
        foreach ($rows as $key => $row) {
            $cols = array();
            foreach ($row as $column) {
                /**@var DBVarchar $column*/
                //                $columns = DviPanelGroup::getDVBoxColumns($rows);
                $dvbox = new DVBox();
                $dvbox->style = 'width: 100%';
                $dvbox->add($column->getLabel());
                $dvbox->add($column->getFormField());
                $cols[] = new DGridColumn($dvbox);
            }
            $rows[$key] = $cols;
        }

        $this->form_rows = $rows;
    }

    public function getFormRows()
    {
        return $this->form_rows;
    }

    private function buildField(string $name, $field)
    {
        parent::addAttribute($name);

        $this->$name = $field;

        $this->addAttributeField($field);

        return $this->$name;
    }

    private function addAttributeField($field)
    {
        self::$attributes_field[] = $field;
    }

    public static function getAttributeFields()
    {
        return self::$attributes_field;
    }

}
