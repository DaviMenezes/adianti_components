<?php

namespace Dvi\Widget\Form;

use Adianti\Control\TAction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\THBox;
use Adianti\Widget\Container\TNotebook;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Wrapper\BootstrapNotebookWrapper;
use Dvi\Widget\Base\DataGridColumn;
use Dvi\Widget\Base\DGridBootstrap;
use Dvi\Widget\Base\DGridColumn as Col;
use Dvi\Widget\Base\DGridColumn;
use Dvi\Widget\Base\GridElement;
use Dvi\Widget\Bootstrap\Component\DButtonGroup;
use Dvi\Widget\Container\DHBox;
use Dvi\Widget\Container\DVBox;
use Dvi\Widget\IDviWidget;

/**
 * Model DviPanelGroup
 *
 * @version    Dvi 1.0
 * @package    Container
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DviPanelGroup implements IDviWidget
{
    private $className;
    private $notebook;
    private $tpanel;
    private $grid;
    private $form;
    private $hboxButtonsFooter;
    private $form_data;


    public function __construct(string $className, string $title = null, string $formName = null)
    {
        $this->className = $className;

        $this->form = new TForm($this->className.'_form_'.($formName ?? uniqid()));
        $this->form->class = 'form-horizontal';
        $this->form->add($this->getGrid());

        $this->tpanel = new TPanelGroup($title);
        $this->tpanel->class .= ' dvi';
        $this->tpanel->style = 'margin-bottom:10px';
        $this->hboxButtonsFooter = new DHBox;

        $this->tpanel->add($this->form);
        $this->tpanel->addFooter($this->hboxButtonsFooter);
    }

    public static function create($class, string $title = null, string $formName = null)
    {
        $className = self::getClassName($class);
        $obj = new DviPanelGroup($className, $title, $formName);
        return $obj;
    }
    /* PROPERTIES***************************************/

    /* PROPERTIES***************************************/
    public function addArrayFields(array $fields)
    {
        if (count($fields) > 0) {
            foreach ($fields as $key => $value) {
                $this->addCols($key, $value);
            }
        }
        return $this;
    }
    public function addNotebook()
    {
        $notebook = new TNotebook();
        $this->notebook = new BootstrapNotebookWrapper($notebook);
        $this->form->add($this->notebook);

        return $this;
    }

    public function appendPage(string $title)
    {
        $this->grid = new DGridBootstrap();
        $this->getNotebook()->appendPage($title, $this->grid);

        return $this;
    }

    /**
     * @return TNotebook
    */
    public function getNotebook()
    {
        return $this->notebook;
    }

    public function addRow(array $param)
    {
        $qtd_columns = count($param);
        $qtd_labels = 0;
        $qtd_cols_to_label = 2;

        $fields = array();

        $dhbox_columns = array();
        $dgrid_collumns = array();
        foreach ($param as $column) {
            if (is_a($column, 'Lib\Dvi\Widget\Base\DGridColumn')) {
                $field = $column->getChilds(0);
                if (is_a($field, 'Lib\Dvi\Widget\Container\DHBox') or is_a($field, 'Lib\Dvi\Widget\Container\DVBox')) {
                    /**@var DHBox $field*/
                    $fields[] = $field->getChilds();
                }

                $dgrid_collumns[] = $column;
            }
        }


        //GET FIELD OF DGRIDCOLUMN AND ADD FIELD IN FORM
        foreach ($param as $column) {
            if (is_a($column, 'Lib\Dvi\Widget\Base\DGridColumn')) {
                /**@var DGridColumn $column */
                if (is_a($column->getChilds(0), TLabel::class)) {
                    $qtd_labels ++;
                }
                $columns[] = $column;

                $fields[] = $column->getChilds(0);
            }
        }

        foreach ($fields as $field) {
            if (is_array($field)) {
                foreach ($field as $item) {
                    $this->addField($item);
                }
            } else {
                $this->addField($field);
            }
        }


        $qtd_anothers = $qtd_columns - $qtd_labels;
        foreach ($columns as $column) {
            if (!$column->getClass()) {
                if (is_a($column->getChilds(0), TLabel::class)) {
                    $column->setClass(Col::MD12);
                    //                    $column->getChilds(0)->class = 'control-label';
                } else {
                    $tt_cols_to_label = $qtd_labels * $qtd_cols_to_label;

                    $tt_cols_to_anothers = (12 - $tt_cols_to_label) / $qtd_anothers;
                    $column->setClass('col-md-'. floor($tt_cols_to_anothers));
                }
            }
        }

        if ($this->needCreateLine($columns)) {
            $row = $this->getGrid()->addRow();
            $row->addCols($columns);
        }
        return $this;
    }

    public function addGroupButton(array $buttons)
    {
        $group = new DButtonGroup();
        $form_name = $this->form->getName();
        foreach ($buttons as $button) {
            $group::add($form_name, [$button[0], $button[1]], $button[2], $button[3]);
        }
        $this->addElement($group);
    }
    /**
     * Add fields in form quickly.
     * Pass the parameters separated with commas
     * @example 1: "Field Name", $field1
     * @example 2: "Date", $dateStart, $dateEnd
     * @example 3: "Complex", [$field1, 'md-8 lg-10','font-color:red'], [$field2,'md-2']
     */
    public function addCols()
    {
        $args = func_get_args();
        $params = (count() == 1) ? func_get_arg(0) : func_get_args();
        //todo possibilidades [
        //  array de DGridColumn
        //]
        // verificar forma de obrigar a inserir as params corretos
        // ex: addCols([new Col($tfield, $class, $style)]))
        //addHidden([$field]
        if (count($params) == 1) {
            $rows_columns[0] = $params;
        } else {
            $rows_columns = $params;
        }

        $has_visible_field = $this->hasVisibleField($rows_columns);

        $columns = array();
        foreach ($rows_columns as $key => $column) {
            $columnElement = $this->createColumnElement($column);
            $columnClass = (is_array($column) and isset($column[2])) ? $column[2] : null;
            $columnStyle = (is_array($column) and isset($column[3])) ? $column[3] : null;
            $gridColumn = new DGridColumn($columnElement, $columnClass, $columnStyle);
            $element = $columnElement->getElement();
            $this->addField($element);

            if ($has_visible_field) {
                $columns[$key] = $gridColumn;
            }
        }

        if ($this->needCreateLine($columns)) {
            $row = $this->getGrid()->addRow();
            $row->addCols($columns);
        }
        return $this;
    }

    private function validateFormField($field)
    {
        if (!is_subclass_of($field, 'TField')) {
            return false;
        }

        $whiteList = ['THidden', 'TEntry', 'TButton', 'TCheckGroup', 'TColor', 'TCombo', 'TDate', 'TDateTime',
            'THidden', 'THtmlEditor', 'TMultiField', 'TFile', 'TMultiFile', 'TPassword', 'TRadioGroup',
            'TSeekButton', 'TDBSeekButton', 'TDBCombo', 'TSelect', 'TSlider', 'TSpinner', 'TText','DCKEditor', 'DText',
            'DEntry', 'DDate', 'DCombo'];

        $className = self::getClassName($field);

        if (in_array($className, $whiteList)) :
            return true;
        endif;

        return false;
    }

    private function createColumnElement($field) : GridElement
    {
        if (is_array($field)) {
            $gridElement = new GridElement($field[0]);
        } else {
            $gridElement = new GridElement($field);
        }
        return $gridElement;
    }

    public function getGrid() : DGridBootstrap
    {
        if (empty($this->grid)) {
            $this->grid = new DGridBootstrap();
        }
        return $this->grid;
    }

    public function show()
    {
        $this->tpanel->show();
    }

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

    public function addDVBox(array $param_columns)
    {
        foreach ($param_columns as $column) {
            $dvbox = new DVBox();
            $dvbox->style = 'width: 100%';

            if (is_array($column[0])) {
                $fields = $column[0];
                foreach ($fields as $field) {
                    $dvbox->add($field);
                }
            } else {
                $dvbox->add($column[0]);
            }
            $width_column = $column[1] ?? 'col-md-'. floor(12 / count($param_columns));
            $columns[] = new Col($dvbox, $width_column);
        }

        $this->addRow($columns);

        //        $params = (count(func_get_args()) == 1) ? func_get_arg(0) : func_get_args();
        //
        //        //todo o primeiro param pode ser TField e string, o segundo apenas TField e o terceiro apenas string
        //        //todo se não validar lançar excessao
        //        if (!is_array($params[0])) {
        //            $field_name = $params[0];
        //            $field_obj = $params[1];
        //            $dvbox_elements[] = DVBox::pack($field_name, $field_obj);
        //            $this->form->addField($field_obj);
        //            $this->addCols($dvbox_elements);
        //            return $this;
        //        }
        //
        //        foreach ($params as $elements) {
        //            if (is_array($elements)) {
        //                $field_name = $elements[0];
        //                $field_obj = $elements[1];
        //                $column_class = $elements[2] ?? null;
        //            }
        //
        //            $dvbox = DVBox::pack($field_name, $field_obj);
        //            $dvbox_elements[] = $dvbox;
        //            $columns[] = [$field_name, $field_obj, $column_class];
        //            $this->form->addField($field_obj);
        //        }
        //        $this->addCols($dvbox_elements);
        return $this;
    }

    public function addHBox($tentry5, $tentry6, $ttext)
    {
        $params = (count(func_get_args()) == 1) ? func_get_arg(0) : func_get_args();
        $hbox = new THBox();
        foreach ($params as $field) {
            $hbox->add($field)->style = 'display:block';
            $this->form->addField($field);
        }
        $this->addCols($hbox);
        return $this;
    }

    private static function getClassName($field)
    {
        $instanceClass = (string) \get_class($field);
        $array = explode('\\', $instanceClass);
        $classType = array_pop($array);

        return $classType;
    }

    /********* ACTIONS ********************/
    public function addActionSave(string $saveMethod = 'onSave', array $parameters = null, $tip = null)
    {
        $this->addQuickAction('btnSave', [$this->className, $saveMethod], 'fa:floppy-o fa-2x', $parameters, $tip);

        return $this;
    }

    public function addActionEdit(array $callback = null, array $parameters = null, $tip = null)
    {
        $action[] = $callback[0] ?? $this->className;
        $action[] = $callback[1] ?? 'onEdit';

        $this->addQuickAction('btnEdit', $action, 'fa:pencil fa-2x', $parameters, $tip);

        return $this;
    }

    public function addActionClear(string $clearMethod = 'onClear', array $parameters = null, $tip = null)
    {
        $this->addQuickAction('btnClear', [$this->className, $clearMethod], 'fa:refresh fa-2x', $parameters, $tip);

        return $this;
    }

    public function addActionSearch($searchMethod = 'onSearch', array $parameters = null, $tip = null)
    {
        $this->addQuickAction('btnSearch', [$this->className, $searchMethod], 'fa:search fa-2x', $parameters, $tip);

        return $this;
    }

    public function addQuickAction(string $id, array $callback, string $image, array $parameters = null, $tip = null, $label = null)
    {
        $data = ['type' => 'button', 'id' => $id, 'callback' => $callback, 'image' => $image, 'parameters' => $parameters, 'tip' => $tip, 'label' => $label];

        $btn = $this->createButton($data);
        $this->hboxButtonsFooter->addButton($btn);

        $this->form->addField($btn);

        return $this;
    }

    private function createButton($value)
    {
        if ($value['type'] == 'button') {
            $btn = new TButton($value['id']);
            $btn->setAction(new TAction($value['callback'], $value['parameters']));

            if (isset($value['label']) and $value['label']) {
                $element_label = new TElement('div');
                $element_label->add($value['label']);
                $element_label->style = 'float: right; margin: 4px 0 0 4px';
                $btn->setLabel($element_label);
            } else {
                $btn->setLabel($value['label']);
            }
            $btn->setImage($value['image']);
            $btn->setTip($value['tip']);
            $btn->style = 'font-size: 14px;';
        } elseif ($value['type'] == 'link') {
            $action = new TAction($value['callback'], $value['parameters']);
            $btn = new TActionLink($value['label'], $action, '#333', '', '', $value['image']);
            $btn->class = $value['class'];
        }

        return $btn;
    }

    private function addField($param)
    {
        $fields = array();
        if (is_a($param, 'DHBox')) {
            /**@var DHBox $form_elements*/
            $form_elements = $param;
            $fields = $form_elements->getFormElements();
        } elseif (count($param) == 1) {
            $fields[] = $param;
        }

        foreach ($fields as $field) {
            if (!$this->validateFormField($field)) {
                continue;
            }

            if (is_a($field, 'THidden')) {
                $this->form->add($field); //important to get data via $param
                $this->form->addField($field);
            } else {
                $this->form->addField($field);
            }
        }
    }

    private function needCreateLine($columns)
    {
        if (count($columns) == 0) {
            return false;
        }

        foreach ($columns as $column) {
            /**@var DGridColumn $element*/
            $element =$column->getChilds(0);
            if (!is_a($element, 'THidden')) {
                return true;
            }
        }
        return false;
    }

    private function hasVisibleField($fields)
    {
        foreach ($fields as $field) {
            if (!empty($field) and !is_a($field, 'THidden') and !is_a($field, 'TLabel')) {
                return true;
            }
        }
        return false;
    }
    /********* END ACTIONS ********************/

    public function setNotebookPageAction(array $callback, array $parameters = null)
    {
        $this->notebook->setTabAction(new TAction($callback, $parameters));

        return $this;
    }

    public function setCurrentNotebookPage(int $index)
    {
        $this->notebook->setCurrentPage($index);

        return $this;
    }

    public function addElement($element)
    {
        $row = $this->getGrid()->addRow();
        $row->addCols([new DGridColumn($element)]);
        return $this;
    }

    public function addHiddenFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->form->add($field); //important to get data via $param
            $this->form->addField($field);
        }

        return $this;
    }

}
