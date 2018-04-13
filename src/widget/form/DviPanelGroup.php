<?php

namespace Dvi\Adianti\Widget\Form;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Base\TElement;
use Adianti\Base\Lib\Widget\Container\THBox;
use Adianti\Base\Lib\Widget\Container\TNotebook;
use Adianti\Base\Lib\Widget\Container\TPanelGroup;
use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Adianti\Base\Lib\Widget\Form\TButton;
use Adianti\Base\Lib\Widget\Form\TCheckGroup;
use Adianti\Base\Lib\Widget\Form\TColor;
use Adianti\Base\Lib\Widget\Form\TCombo;
use Adianti\Base\Lib\Widget\Form\TDate;
use Adianti\Base\Lib\Widget\Form\TDateTime;
use Adianti\Base\Lib\Widget\Form\TEntry;
use Adianti\Base\Lib\Widget\Form\TField;
use Adianti\Base\Lib\Widget\Form\TFile;
use Adianti\Base\Lib\Widget\Form\TForm;
use Adianti\Base\Lib\Widget\Form\THidden;
use Adianti\Base\Lib\Widget\Form\THtmlEditor;
use Adianti\Base\Lib\Widget\Form\TLabel;
use Adianti\Base\Lib\Widget\Form\TMultiField;
use Adianti\Base\Lib\Widget\Form\TMultiFile;
use Adianti\Base\Lib\Widget\Form\TNumeric;
use Adianti\Base\Lib\Widget\Form\TPassword;
use Adianti\Base\Lib\Widget\Form\TRadioGroup;
use Adianti\Base\Lib\Widget\Form\TSeekButton;
use Adianti\Base\Lib\Widget\Form\TSelect;
use Adianti\Base\Lib\Widget\Form\TSlider;
use Adianti\Base\Lib\Widget\Form\TSpinner;
use Adianti\Base\Lib\Widget\Form\TText;
use Adianti\Base\Lib\Widget\Wrapper\TDBCombo;
use Adianti\Base\Lib\Widget\Wrapper\TDBSeekButton;
use Adianti\Base\Lib\Wrapper\BootstrapNotebookWrapper;
use Dvi\Adianti\Control\DAction;
use Dvi\Adianti\Route;
use Dvi\Adianti\Widget\Base\DGridBootstrap;
use Dvi\Adianti\Widget\Base\DGridColumn;
use Dvi\Adianti\Widget\Base\DGridColumn as Col;
use Dvi\Adianti\Widget\Base\GridElement;
use Dvi\Adianti\Widget\Base\GroupField;
use Dvi\Adianti\Widget\Bootstrap\Component\DButtonGroup;
use Dvi\Adianti\Widget\Container\DHBox;
use Dvi\Adianti\Widget\Container\DVBox;
use Dvi\Adianti\Widget\IDviWidget;
use Dvi\Adianti\Widget\IGroupField;
use Dvi\Adianti\Widget\Util\DActionLink;

/**
 * Model DviPanelGroup
 *
 * @version    Dvi 1.0
 * @package    Container
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DviPanelGroup implements IDviWidget
{
    protected $className;
    protected $notebook;
    protected $tpanel;
    protected $grid;
    protected $form;
    protected $hboxButtonsFooter;
    protected $form_data;
    protected $btn;

    protected $useLabelFields = false;
    private $footer_items = array();

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

        foreach ($param_columns as $fields) {
            $dvbox = new DVBox();
            $dvbox->style = 'width: 100%';

            if (is_array($fields)) {
                foreach ($fields as $field) {
                    $dvbox->add($field);
                }
            } else {
                $dvbox->add($fields);
            }
            $width_column = $fields[1] ?? 'col-md-' . floor(12 / count($param_columns));
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

    public function addRow(array $columns)
    {
        $this->validateColumnType($columns);

        $qtd_labels = 0;

        //GET FIELD OF DGRIDCOLUMN AND ADD FIELD IN FORM
        /**@var DGridColumn $column */
        foreach ($columns as $column) {
            /**@var GroupField $child*/
            $child = $column->getChilds(0);
            $columnElements[] = $child;

            if (is_a($child, TLabel::class)) {
                $qtd_labels ++;
            }
        }

        $this->addFormFields($columns);

        $this->PrepareColumnClass($qtd_labels, $columns);

        if ($this->needCreateLine($columns)) {
            $row = $this->getGrid()->addRow();

            foreach ($columns as $column) {
                $column->useLabelField($this->useLabelFields);
                $row->addCol($column);
            }
            //            $row->addCols($columns);
        }
        return $this;
    }

    public function addGroupButton(array $buttons)
    {
        $group = new DButtonGroup();
        $form_name = $this->form->getName();
        foreach ($buttons as $button) {
            $group->add($form_name, [$button[0], $button[1]], $button[2], $button[3]);
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
        //        $instanceClass = (string) \get_class($field);
        $array = explode('\\', $field);
        $classType = array_pop($array);

        return $classType;
    }

    #region [ACTIONS] ***********************************************
    public function addActionSave(
        string $label = 'Save',
        string $saveMethod = 'onSave',
        array $parameters = null,
        $tip = null
    ) {
        $str_label = !empty($label) ? _t($label) : null;
        $this->addCustomAction(
            [$this->className, $saveMethod],
            'fa:floppy-o fa-2x',
            $str_label,
            $parameters,
            $tip,
            'btnSave'
        );

        return $this;
    }

    public function addFooterItem($obj)
    {
        $this->footer_items[] = $obj;

        return $this;
    }

    public function addActionClear(
        string $label = 'Clear',
        string $clearMethod = 'onClear',
        array $parameters = null,
        $tip = null
    ) {
        $str_label = !empty($label) ? _t($label) : null;
        $this->addCustomAction(
            [$this->className, $clearMethod],
            'fa:eraser fa-2x',
            $str_label,
            $parameters,
            $tip,
            'btnClear'
        );

        return $this;
    }

    public function addActionSearch(
        string $label = 'Search',
        $searchMethod = 'onSearch',
        array $parameters = null,
        $tip = null
    ) {
        $this->addCustomAction(
            [$this->className, $searchMethod],
            'fa:search fa-2x',
            _t($label),
            $parameters,
            $tip,
            'btnSearch'
        );

        return $this;
    }

    public function addCustomAction(
        array $callback,
        string $image,
        $label = null,
        array $parameters = null,
        $tip = null,
        string $id = null
    ) {
        $id = $id ?? uniqid().time();
        $data = [
            'type' => 'button',
            'id' => $id,
            'callback' => $callback,
            'image' => $image,
            'parameters' => $parameters,
            'tip' => $tip,
            'label' => $label];

        $this->btn = $this->createButton($data);
        $this->hboxButtonsFooter->addButton($this->btn);

        $this->form->addField($this->btn);

        return $this;
    }

    public function addCustomActionLink(
        array $callback,
        string $image,
        $label = null,
        array $parameters = null,
        $tip = null,
        string $class = 'btn btn-default'
    ) {
        $data = [
            'type' => 'link',
            'class' => $class,
            'callback' => $callback,
            'image' => $image,
            'parameters' => $parameters,
            'tip' => $tip,
            'label' => $label
        ];
        $btn = $this->createButton($data);
        $this->hboxButtonsFooter->addButton($btn);

        return $this;
    }

    public function addActionBackLink(array $action = null):DActionLink
    {
        $btn = new DActionLink($action, _t('Back'), 'fa:arrow-left fa-2x');
        $btn->class = 'btn btn-default';

        $this->hboxButtonsFooter->addButton($btn);

        return $btn;
    }
    #endregion

    private function createButton($value)
    {
        if ($value['type'] == 'button') {
            $btn = new DButton($value['id']);
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
            $action = new DAction($value['callback'], $value['parameters']);
            $label = $value['label'];
            $icon = $value['image'];
            $btn = new DActionLink($action, $label, $icon);
            $btn->class = $value['class'];
        }

        return $btn;
    }

    public function getButton():DButton
    {
        return $this->btn;
    }

    private function addFormFields($columns)
    {
        $fields = $this->getComponentFields($columns);

        foreach ($fields as $field) {
            if (!$this->validateFormField($field)) {
                continue;
            }
            $this->addField($field);
        }
    }

    private function addField($field)
    {
        if (is_a($field, 'THidden')) {
            $this->form->add($field); //important to get data via $param
        }

        $this->form->addField($field);
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

    protected function getWhiteList(): array
    {
        $whiteList = [
            THidden::class,
            TEntry::class,
            DEntry::class,
            TButton::class,
            DButton::class,
            TCheckGroup::class,
            TColor::class,
            TCombo::class,
            DCombo::class,
            TDate::class,
            DDate::class,
            TDateTime::class,
            THidden::class,
            THtmlEditor::class,
            TMultiField::class,
            TFile::class,
            TMultiFile::class,
            TPassword::class,
            TRadioGroup::class,
            DRadioGroup::class,
            TSeekButton::class,
            TDBSeekButton::class,
            TDBCombo::class,
            TSelect::class,
            TSlider::class,
            TSpinner::class,
            TNumeric::class,
            TText::class,
            DText::class,
            DCKEditor::class
        ];
        return $whiteList;
    }

    private function isGroupField($element)
    {
        if ($element instanceof IGroupField) {
            return true;
        }
        return false;
    }

    private function PrepareColumnClass($qtd_labels, $columns)
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

    private function validateColumnType($param)
    {
        foreach ($param as $item) {
            if (!is_a($item, DGridColumn::class)) {
                new TMessage('error', 'Todas as colunas devem ser do tipo ' . DGridColumn::class);
                die();
            }
        }
    }

    private function extractColumnsComponents($columns): array
    {
        $columnElements = array();

        /**@var DGridColumn $column*/
        foreach ($columns as $column) {
            $columnElements[] = $column->getChilds(0);
        }
        return $columnElements;
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

    public function useLabelFields(bool $bool)
    {
        $this->useLabelFields = $bool;
    }
}
