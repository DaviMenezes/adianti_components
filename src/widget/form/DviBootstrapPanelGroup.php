<?php

namespace Dvi\Adianti\Widget\Form;

use Adianti\Control\TAction;
use Adianti\Widget\Container\TNotebook;
use Adianti\Widget\Container\TPanelGroup;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TForm;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Util\TActionLink;
use Adianti\Wrapper\BootstrapNotebookWrapper;
use Dvi\Adianti\Widget\Base\DGridRow;
use Dvi\Adianti\Widget\Base\GridElement;
use Dvi\Adianti\Widget\Base\GridNotebook;
use Dvi\Adianti\Widget\Container\DHBox;
use Dvi\Adianti\Widget\Container\DVBox;
use Dvi\Adianti\Widget\Base\DGridBootstrap;

/**
 * Cria painéis com formulários personalisados
 *
 * @version    1.0
 * @package    grid bootstrap
 * @subpackage base
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DviBootstrapPanelGroup
{
    public $tpanel;
    private $className;
    private $gridRows;
    private $actions;
    private $actionButtons;
    private $hboxButtonsFooter;
    private $gridfields;
    private $hiddenFields = array();
    private $bootstrapClassDefault;
    /**@var TForm $form*/
    private $form;
    private $formfields = array();
    private $tnotebook;
    private $tnotebookPage;
    private $tnotebookPages = array();

    /* news props test*/
    private $gridNotebook;
    private $current_tnotebookPage;
    //*****************

    /**
     * Ajuda a construir formulários com grid bootstrap customizáveis com rapidez
     * @param $className = The name current class
     * @param $title = The title of the panel Group
     * @param $columnTypeDefault = Bootstrap column class type (xs, sm, md, lg). Default is md
     * @param $methods = especific action methods [onSearch, onNew, onSave or onClear]
     */
    public function __construct(string $className, string $title = null, string $columnTypeDefault = 'md', array $methods = null)
    {
        $this->tpanel = new TPanelGroup($title);
        $this->tpanel->class .= ' dvi';

        $this->className = $className;
        $this->bootstrapClassDefault = $columnTypeDefault . '-';

        $this->setForm();

        $this->gridfields = new DGridBootstrap;

        $this->setMethods($methods);

        //news props in test
        $this->gridNotebook = new GridNotebook();
    }

    public function addFooterAction($callback, $image, $id = null, array $parameters = null, $tip = null, $label = null)
    {
        $this->addQuickAction($id ?? 'btn_' . uniqid(), $callback, $image, $parameters, $tip, $label);

        return $this;
    }

    public function addActionSearch($searchMethod = 'onSearch', array $parameters = null, $tip = null)
    {
        $this->addQuickAction('btnSearch', [$this->className, $searchMethod], 'fa:search fa-2x', $parameters, $tip);

        return $this;
    }

    //@param $callback Default is [$this->className,'onEdit']
    public function addActionNew(array $callback = null, array $parameters = null, $tip = null)
    {
        $action[] = $callback[0] ?? $this->className;
        $action[] = $callback[1] ?? 'onEdit';

        $this->addQuickAction('btnNew', $action, 'fa:plus fa-2x', $parameters, $tip);

        return $this;
    }

    public function addActionSave(string $saveMethod = 'onSave', array $parameters = null, $tip = null)
    {
        $this->addQuickAction('btnSave', [$this->className, $saveMethod], 'fa:floppy-o fa-2x', $parameters, $tip);

        return $this;
    }

    public function addActionClear(string $clearMethod = 'onClear', array $parameters = null, $tip = null)
    {
        $this->addQuickAction('btnClear', [$this->className, $clearMethod], 'fa:refresh fa-2x', $parameters, $tip);

        return $this;
    }

    public function addActionBackToList(string $className, string $method = 'onReload', $tip = null, array $parameters = null, $image = null, $label = null)
    {
        $this->addQuickAction('btnBack', [$className, $method], $image ?? 'fa:arrow-left fa-2x', $parameters, $tip, $label);

        return $this;
    }

    public function addActionLinkBackToList(array $callback, array $parameters, string $label = null, string $image = 'fa:arrow-left fa-2x')
    {
        $this->addActionButtonLink($label ? _t($label) : '', $callback, $parameters, $image);

        return $this;
    }

    /**
     * Add fields no necessary in Form
     */
    public function addOffQuickFields()
    {
        $args = ['type' => 'off', 'fields' => func_get_args()];
        $this->addFieldForm($args);

        return $this;
    }

    
    public function addDVBox()
    {
        $params = (count(func_get_args()) == 1) ? func_get_arg(0) : func_get_args();
        
        $array = array();
        foreach ($params as $elements) {
            $field_name = $elements[0];
            $field_obj = $elements[1];
            $dvbox_elements[] = DVBox::pack($field_name, $field_obj);
            $this->form->addField($field_obj);
        }
        $this->addFields($dvbox_elements);
        return $this;
    }
    
    /**
     * Add fields in form quickly.
     * Pass the parameters separated with commas
     * @example 1: "Field Name", $field1
     * @example 2: "Date", $dateStart, $dateEnd
     * @example 3: "Complex", [$field1, 'md-8 lg-10','font-color:red'], [$field2,'md-2']
     */
    public function addFields()
    {
        $params = (count(func_get_args()) == 1) ? func_get_arg(0) : func_get_args();

        foreach ($params as $key => $field) {
            $columns[$key] = $this->createColumnElement($field);
        }

        $this->gridRows[] = ['tnotebookPage' => $this->tnotebookPage, 'cols' => $columns];

        return $this;
    }

    /**
     * Alias to addFields
     */
    public function addF()
    {
        $this->addFields(func_get_args());

        return $this;
    }
    
    public function addArrayFields(array $fields)
    {
        if (count($fields) > 0) {
            foreach ($fields as $key => $value) {
                if (is_a($value, 'THidden')) {
                    $this->addHiddenField($value);
                } else {
                    $this->addFields([$key], [$value]);
                }
            }
        }
    }

    public function addHiddenField($field)
    {
        $this->hiddenFields[] = $field;

        return $this;
    }

    public function addHiddenFields()
    {
        $fields = func_get_args();
        if ($fields) {
            foreach ($fields[0] as $field) {
                $this->hiddenFields[] = $field;
            }
        }
        return $this;
    }

    public function addActionButtonLink(string $label, array $callback, array $parameters, string $image)
    {
        $this->actions[] = ['type' => 'link', 'callback' => $callback, 'image' => $image, 'parameters' => $parameters, 'label' => $label, 'class' => 'btn btn-default'];

        return $this;
    }

    private function addQuickAction(string $id, array $callback, string $image, array $parameters = null, $tip = null, $label = null)
    {
        $this->actions[] = ['type' => 'button', 'id' => $id, 'callback' => $callback, 'image' => $image, 'parameters' => $parameters, 'tip' => $tip, 'label' => $label];

        return $this;
    }

    private function setForm()
    {
        $this->form = new TForm('form_'.$this->className);
        $this->form->class = 'form-horizontal';
        //        $this->form = $form;
    }

    public function getForm() : TForm
    {
        return $this->form;
    }

    public function setFormData()
    {
        $this->form->setData($this->form->getData());
    }

    public function createGrid()
    {
        $this->createRowsColumns();

        $this->createNotebookPages();

        $this->addFieldsOnForm();

        if ($this->tnotebookPage) {
            $this->form->add($this->tnotebook);
        } else {
            $this->form->add($this->gridfields);
        }

        $this->createActionsFooter();

        $this->tpanel->add($this->form);

        if ($this->hboxButtonsFooter) {
            $this->tpanel->addFooter($this->hboxButtonsFooter);
        }
    }

    private function createNotebookPages()
    {
        if ($this->tnotebookPage) {
            $this->tnotebook = new BootstrapNotebookWrapper(new TNotebook);

            foreach ($this->tnotebookPages as $pages) {
                $content_page = $pages['grid'];
                $content_page->style = 'padding:5px';
                $this->tnotebook->appendPage($pages['title'], $content_page);
            }
        }
    }

    private function createRowsColumns()
    {
        $this->current_tnotebookPage = null;
        //create Columns on the grid in format(col1 = label, col2 = any field, col3 = any field, etc)
        foreach ($this->gridRows as $key => $columns) {
            $this->createNotebookPageIfNecessary($columns);

            $row = $this->gridfields->addRow();
            $cols = array_column($columns, 'cols');
            $row->addCols($columns['cols']);
            //$this->createRow($columns);
        }
    }

    /**
     * Get fields of the GridRows and add in form if it is a valid type
     */
    private function addFieldsOnForm()
    {
        $this->manageFieldByType();

        $this->getHiddenFields();

        $this->getButtonsFields();
        //Todo esse codigo pode se juntar ao do metodo getButtonsFields
        foreach ($this->formfields as $field) {
            if ($this->validateField($field)) {
                $this->form->addField($field);
            }
        }
    }

    private function createActionsFooter()
    {
        if ($this->actionButtons) {
            $this->hboxButtonsFooter = new DHBox;
            foreach ($this->actionButtons as $button) {
                $this->hboxButtonsFooter->addButton($button);
            }
        }
    }

    public function appendPage($title)
    {
        $this->tnotebookPage = $title;

        return $this;
    }

    private function manageFieldByType()
    {
        $cols = array_column($this->gridRows, 'cols')[0];
        foreach ($cols as $column) {
            $this->addComponentInFormByType($column);
        }
        //        foreach ($this->gridRows as $row) {
//            foreach ($row['cols'] as $column) {
//                $this->addComponentInFormByType($column);
//            }
//        }
    }

    private function getHiddenFields()
    {
        foreach ($this->hiddenFields as $field) {
            $this->form->add($field); //important to get data via $form->getData()
            $this->form->addField($field); //important to get data via $param
        }
    }

    //ACTIONS BUTTONS
    private function getButtonsFields()
    {
        if ($this->actions) :
            foreach ($this->actions as $value) {
                $btn = $this->createButton($value);
                $this->actionButtons[] = $btn;
                $this->formfields[] = $btn;
            }
        endif;
    }

    private function addFieldForm(array $args)
    {
        $rowColumns = array();
        $element = array();

        $this->createElement($args);
        return;
        foreach ($args['fields'] as $key => $arg) {
            if (is_array($arg)) {
                $element['field'] = $arg[0];
                $element['class'] = $arg[1] ?? null;
                $element['style'] = $arg[2] ?? null;
            } else {
                $element['field'] = $arg;
            }
            $element['type'] = $args['type'];

            $rowColumns[$key] = $element;
        }
        
        if (count($element)== 0) {
            return;
        }

        $this->gridRows[] = ['tnotebookPage' => $this->tnotebookPage, 'cols' => $rowColumns];
    }

    private function createElement(array $args)
    {
        $elements = array();
        foreach ($args['fields'] as $key => $arg) {
            $elements[] = new GridElement($arg[0], $arg[1] ?? null, $arg[2] ?? null);
        }
        return $elements;
    }

    public function setCurrentNotebookPage(int $index)
    {
        $this->tnotebook->setCurrentPage($index);

        return $this;
    }

    public function setNotebookPageAction(array $callback, array $parameters = null)
    {
        $this->tnotebook->setTabAction(new TAction($callback, $parameters));

        return $this;
    }

    private function validateField($field)
    {
        if (!is_subclass_of($field, 'TField')) {
            return false;
        }

        $whiteList = ['THidden', 'TEntry', 'TButton', 'TCheckGroup', 'TColor', 'TCombo', 'TDate', 'TDateTime',
            'THidden', 'THtmlEditor', 'TMultiField', 'TFile', 'TMultiFile', 'TPassword', 'TRadioGroup',
            'TSeekButton', 'TDBSeekButton', 'TSelect', 'TSlider', 'TSpinner', 'TText','DCKEditor'];

        $className = $this->getClassName($field);

        if (in_array($className, $whiteList)) :
            return true;
        endif;

        return false;
    }

    private function createButton($value)
    {
        if ($value['type'] == 'button') {
            $btn = new TButton($value['id']);
            $btn->setAction(new TAction($value['callback'], $value['parameters']));
            $btn->setLabel($value['label']);
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

    private function getClassName($field)
    {
        $instanceClass = (string) \get_class($field);
        $array = explode('\\', $instanceClass);
        $classType = array_pop($array);
        
        return $classType;
    }

    private function createNotebookPageIfNecessary($columns)
    {
        if ($columns['tnotebookPage'] != $this->current_tnotebookPage) {
            $this->current_tnotebookPage = $columns['tnotebookPage'];

            $this->gridfields = new DGridBootstrap;

            $this->tnotebookPages[] = ['title' => $this->current_tnotebookPage, 'grid' => $this->gridfields];
        }
    }

    private function setMethods($methods)
    {
        if (isset($methods['onSearch'])) {
            $this->addActionSearch($methods['onSearch']);
        }
        if (isset($methods['onNew'])) {
            $this->addActionNew($methods['onNew']);
        }
        if (isset($methods['onClear'])) {
            $this->addActionClear($methods['onClear']);
        }
        if (isset($methods['onSave'])) {
            $this->addActionSave($methods['onSave']);
        }
    }

    private function addComponentInFormByType(GridElement $column)
    {
        if (is_a($column->getElement(), 'TDataGrid')) :
            $this->form->add($column['field']); elseif ($column->insideForm == 'inside') :
            $this->formfields[] = $column->getElement();
        endif;
    }

    private function createColumnElement($field)
    {
        if (is_array($field)) {
            $gridElement = new GridElement($field[0]);
        } else {
            $gridElement = new GridElement($field);
        }
        return $gridElement;
    }

    public function show()
    {
        $this->tpanel->show();
    }
}
