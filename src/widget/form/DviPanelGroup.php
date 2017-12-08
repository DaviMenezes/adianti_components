<?php

namespace Dvi\Adianti\Widget\Form;

use Adianti\Control\TAction;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\THBox;
use Adianti\Widget\Container\TNotebook;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TCheckGroup;
use Adianti\Widget\Form\TColor;
use Adianti\Widget\Form\TCombo;
use Adianti\Widget\Form\TDate;
use Adianti\Widget\Form\TDateTime;
use Adianti\Widget\Form\TEntry;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\TFile;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\THidden;
use Adianti\Widget\Form\THtmlEditor;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Form\TMultiField;
use Adianti\Widget\Form\TMultiFile;
use Adianti\Widget\Form\TPassword;
use Adianti\Widget\Form\TRadioGroup;
use Adianti\Widget\Form\TSeekButton;
use Adianti\Widget\Form\TSelect;
use Adianti\Widget\Form\TSlider;
use Adianti\Widget\Form\TSpinner;
use Adianti\Widget\Form\TText;
use Adianti\Widget\Wrapper\TDBCombo;
use Adianti\Widget\Wrapper\TDBSeekButton;
use Adianti\Wrapper\BootstrapNotebookWrapper;
use Dvi\Adianti\Route;
use Dvi\Adianti\Widget\Base\DGridBootstrap;
use Dvi\Adianti\Widget\Base\DGridColumn;
use Dvi\Adianti\Widget\Base\DGridColumn as Col;
use Dvi\Adianti\Widget\Base\GridElement;
use Dvi\Adianti\Widget\Bootstrap\Component\DButtonGroup;
use Dvi\Adianti\Widget\Container\DHBox;
use Dvi\Adianti\Widget\Container\DVBox;
use Dvi\Adianti\Widget\IDviWidget;
use Dvi\Adianti\Widget\Util\DActionLink;

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
        $this->className = Route::getClassName($className);

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

    public static function getDVBoxColumns(array $param_columns): array
    {
        $columns = array();

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
            $width_column = $column[1] ?? 'col-md-' . floor(12 / count($param_columns));
            $columns[] = new Col($dvbox, $width_column);
        }
        return $columns;
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

        $fields = $this->getColumnFields($param);

        $columns = array();
        //GET FIELD OF DGRIDCOLUMN AND ADD FIELD IN FORM
        foreach ($param as $column) {
            if (is_a($column, DGridColumn::class)) {
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
        if (!is_subclass_of($field, TField::class)) {
            return false;
        }

        $whiteList = $this->getWhiteList();

        if (in_array(get_class($field), $whiteList)) :
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
        $columns = self::getDVBoxColumns($param_columns);

        $this->addRow($columns);

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

    #region [ACTIONS] ***********************************************
    public function addActionSave(string $label = 'Save', string $saveMethod = 'onSave', array $parameters = null, $tip = null)
    {
        $str_label = !empty($label) ? _t($label) : null;
        $this->addCustomAction([$this->className, $saveMethod], 'fa:floppy-o fa-2x', $str_label, $parameters, $tip, 'btnSave');

        return $this;
    }

    public function addActionClear(string $label = 'Clear', string $clearMethod = 'onClear', array $parameters = null, $tip = null)
    {
        $str_label = !empty($label) ? _t($label) : null;
        $this->addCustomAction([$this->className, $clearMethod], 'fa:refresh fa-2x', $str_label, $parameters, $tip, 'btnClear');

        return $this;
    }

    public function addActionSearch(string $label = 'Search', $searchMethod = 'onSearch', array $parameters = null, $tip = null)
    {
        $this->addCustomAction([$this->className, $searchMethod], 'fa:search fa-2x', _t($label), $parameters, $tip, 'btnSearch');

        return $this;
    }

    public function addCustomAction(array $callback, string $image, $label = null, array $parameters = null, $tip = null, string $id = null)
    {
        $id = $id ?? uniqid().time();
        $data = ['type' => 'button', 'id' => $id, 'callback' => $callback, 'image' => $image, 'parameters' => $parameters, 'tip' => $tip, 'label' => $label];

        $btn = $this->createButton($data);
        $this->hboxButtonsFooter->addButton($btn);

        $this->form->addField($btn);

        return $this;
    }

    public function addCustomActionLink(array $callback, string $image, $label = null, array $parameters = null, $tip = null, string $class = 'btn btn-default')
    {
        $data = ['type' => 'link', 'class' => $class, 'callback' => $callback, 'image' => $image, 'parameters' => $parameters, 'tip' => $tip, 'label' => $label];
        $btn = $this->createButton($data);
        $this->hboxButtonsFooter->addButton($btn);

        return $this;
    }
    #endregion

    private function createButton($value)
    {
        if ($value['type'] == 'button') {
            $btn = new TButton($value['id']);
            $btn->setAction(new TAction($value['callback'], $value['parameters']));

            if (isset($value['label']) and $value['label']) {
                $element_label = new TElement('div');
                $element_label->add($value['label']);
                $element_label->class = 'dvi_btn_label';
                $btn->setLabel($element_label);
            } else {
                $btn->setLabel($value['label']);
            }

            $btn->setImage($value['image']);
            $btn->setTip($value['tip']);
            $btn->class = 'btn btn-default dvi_panel_action';
            $btn->style = 'font-size: 14px;';
        } elseif ($value['type'] == 'link') {
            $action = new TAction($value['callback'], $value['parameters']);
            $label = $value['label'];
            $icon = $value['image'];
            $btn = new DActionLink($action, $label, $icon);
            $btn->class = $value['class'];
        }

        return $btn;
    }

    private function addField($param)
    {
        $fields = array();
        if (is_a($param, DHBox::class)) {
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
            }

            $this->form->addField($field);
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
            if (!is_a($element, THidden::class)) {
                return true;
            }
        }
        return false;
    }

    private function hasVisibleField($fields)
    {
        foreach ($fields as $field) {
            if (!empty($field) and !is_a($field, THidden::class) and !is_a($field, TLabel::class)) {
                return true;
            }
        }
        return false;
    }

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

    private function getColumnFields(array $param): array
    {
        $fields = array();
        foreach ($param as $column) {
            if (is_a($column, DGridColumn::class)) {
                $field = $column->getChilds(0);
                if (is_a($field, DHBox::class) or is_a($field, DVBox::class)) {
                    /**@var DHBox $field */
                    $fields[] = $field->getChilds();
                }

                //                $dgrid_collumns[] = $column;
            }
        }
        return $fields;
    }

    private function getWhiteList(): array
    {
        $whiteList = [
            THidden::class,
            TEntry::class,
            TButton::class,
            TCheckGroup::class,
            TColor::class,
            TCombo::class,
            TDate::class,
            TDateTime::class,
            THidden::class,
            THtmlEditor::class,
            TMultiField::class,
            TFile::class,
            TMultiFile::class,
            TPassword::class,
            TRadioGroup::class,
            TSeekButton::class,
            TDBSeekButton::class,
            TDBCombo::class,
            TSelect::class,
            TSlider::class,
            TSpinner::class,
            TText::class,
            DCKEditor::class,
            DText::class,
            DEntry::class,
            DDate::class,
            DCombo::class
        ];
        return $whiteList;
    }
}
