<?php

namespace Dvi\Adianti\Widget\Form\PanelGroup;

use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Adianti\Base\Lib\Widget\Form\TField;
use Adianti\Base\Lib\Widget\Form\TForm;
use Adianti\Base\Lib\Widget\Form\TLabel;
use Dvi\Adianti\Widget\Base\GridColumn;
use Dvi\Adianti\Widget\Base\GroupField;
use Dvi\Adianti\Widget\Form\Col;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;
use Dvi\Adianti\Widget\IGroupField;

/**
 * Form PanelGroupFormInterface
 *
 * @package    Form
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait PanelGroupFormFacade
{
    public function setFormData($data)
    {
        $this->form->setData($data);
    }

    public function keepFormLoaded()
    {
        $this->form->setData($this->form->getData());
    }

    public function getForm() :TForm
    {
        return $this->form;
    }

    public function getFormData()
    {
        return $this->form->getData();
    }

    private function addFormFields($columns)
    {
        $fields = $this->getComponentFields($columns);

        foreach ($fields as $field) {
            if (!$this->validateFormField($field)) {
                continue;
            }
            $this->addFormField($field);
        }
    }

    private function getComponentFields($columns): array
    {
        $components = $this->extractColumnsComponents($columns);

        $fields = array();
        foreach ($components as $element) {
            if ($this->isGroupField($element)) {
                $fields[] = $element;

                /**@var IGroupField $element */
                foreach ($element->getChilds() as $child) {
                    $fields[] = $child;
                };
            } else {
                $fields[] = $element;
            }
        }
        return $fields;
    }

    private function validateFormField($field)
    {
        if (is_a($field, TField::class)) {
            return true;
        }

        return false;
    }

    private function extractColumnsComponents($columns): array
    {
        $columnElements = array();

        /**@var GridColumn $column*/
        foreach ($columns as $column) {
            $columnElements[] = $column->getChilds(0);
        }
        return $columnElements;
    }

    private function isGroupField($element)
    {
        if ($element instanceof IGroupField) {
            return true;
        }
        return false;
    }

    private function addFormField($field)
    {
        if ($this->form->getField($field->getName())) {
            //return if already add field
            return;
        }
        if (is_a($field, 'THidden')) {
            $this->form->add($field); //important to get data via $param
        }

        $this->form->addField($field);
    }

    public function addRow(array $columns)
    {
        $this->validateColumnType($columns);

        $qtd_labels = 0;

        //GET FIELD OF DGRIDCOLUMN AND ADD FIELD IN FORM
        /**@var GridColumn $column */
        foreach ($columns as $column) {
            /**@var GroupField $child*/
            $child = $column->getChilds(0);
            $columnElements[] = $child;

            if (is_a($child, TLabel::class)) {
                $qtd_labels ++;
            }
        }

        $this->addFormFields($columns);

        $this->prepareColumnClass($qtd_labels, $columns);

        if ($this->needCreateLine($columns)) {
            $row = $this->getGrid()->addRow();

            foreach ($columns as $column) {
                $column->useLabelField($this->useLabelFields);
                $row->addCol($column);
            }
        }
        return $this;
    }

    private function validateColumnType($param)
    {
        foreach ($param as $item) {
            if (!is_a($item, GridColumn::class)) {
                throw new \Exception('Todas as colunas devem ser do tipo ' . GridColumn::class);
            }
        }
    }

    private function prepareColumnClass($qtd_labels, $columns)
    {
        $qtd_columns = count($columns);
        $qtd_cols_to_label = 2;

        $qtd_anothers = $qtd_columns - $qtd_labels;
        foreach ($columns as $column) {
            if (!$column->getClass()) {
                /**@var GroupField $child*/
                $child = $column->getChilds(0);

                if (is_a($child, TLabel::class)) {
                    $column->setClass(Col::MD12);
                    //$column->getChilds(0)->class = 'control-label';
                } else {
                    $tt_cols_to_label = $qtd_labels * $qtd_cols_to_label;

                    $tt_cols_to_anothers = (12 - $tt_cols_to_label) / $qtd_anothers;
                    $column->setClass('col-md-' . floor($tt_cols_to_anothers));
                }
            }
        }
    }

    public function addHiddenFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->form->add($field); //important to get data via $param
            $this->form->addField($field);
        }

        return $this;
    }

    public function useLabelFields(bool $bool = false)
    {
        $this->useLabelFields = $bool;
    }
}
