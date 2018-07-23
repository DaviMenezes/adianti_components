<?php

namespace Dvi\Adianti\Widget\Form\PanelGroup;

use Adianti\Base\Lib\Widget\Container\THBox;
use Adianti\Base\Lib\Widget\Container\TPanelGroup;
use Adianti\Base\Lib\Widget\Form\TButton;
use Adianti\Base\Lib\Widget\Form\TCheckGroup;
use Adianti\Base\Lib\Widget\Form\TColor;
use Adianti\Base\Lib\Widget\Form\TCombo;
use Adianti\Base\Lib\Widget\Form\TDate;
use Adianti\Base\Lib\Widget\Form\TDateTime;
use Adianti\Base\Lib\Widget\Form\TEntry;
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
use Dvi\Adianti\Componente\Model\Form\Fields\DNumeric;
use Dvi\Adianti\Route;
use Dvi\Adianti\Widget\Base\DGridBootstrap;
use Dvi\Adianti\Widget\Base\DGridColumn as Col;
use Dvi\Adianti\Widget\Base\GridElement;
use Dvi\Adianti\Widget\Bootstrap\Component\DButtonGroup;
use Dvi\Adianti\Widget\Container\DHBox;
use Dvi\Adianti\Widget\Container\DVBox;
use Dvi\Adianti\Widget\Form\DButton;
use Dvi\Adianti\Widget\Form\DCKEditor;
use Dvi\Adianti\Widget\Form\DCombo;
use Dvi\Adianti\Widget\Form\DDate;
use Dvi\Adianti\Widget\Form\DDateTime;
use Dvi\Adianti\Widget\Form\DEntry;
use Dvi\Adianti\Widget\Form\DHidden;
use Dvi\Adianti\Widget\Form\DHtmlEditor;
use Dvi\Adianti\Widget\Form\DPassword;
use Dvi\Adianti\Widget\Form\DRadioGroup;
use Dvi\Adianti\Widget\Form\DSpinner;
use Dvi\Adianti\Widget\Form\DText;
use Dvi\Adianti\Widget\IDviWidget;
use ReflectionClass;

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

    protected $tpanel;
    protected $grid;
    /**@var TForm $form*/
    protected $form;
    /**@var DHBox $hboxButtonsFooter*/
    protected $hboxButtonsFooter;
    /**@var DButtonGroup $group_actions*/
    protected $group_actions;
    protected $form_data;

    protected $useLabelFields = false;
    protected $footer_items = array();
    protected $footer_item;
    private $title;

    use PanelGroupActionsFacade;
    use PanelGroupFormFacade;
    use PanelGroupNotebookFacade;

    public function __construct(string $className, string $title = null, string $formName = null)
    {
        $this->className = Route::getClassName($className);

        $this->form = new TForm($this->className.'_form_'.($formName ?? uniqid()));
        $this->form->class = 'form-horizontal';
        $this->form->add($this->getGrid());

        $this->title = $title;

        $this->hboxButtonsFooter = new DHBox;

        $this->group_actions = new DButtonGroup($this->form);

    }

    public function setTitle($title)
    {
        $this->title = trim($title);
        return $this;
    }

    public static function create($class, string $title = null, string $formName = null)
    {
        $className = (new ReflectionClass($class))->getShortName();
        $obj = new DviPanelGroup($className, $title, $formName);
        return $obj;
    }

    public function addArrayFields(array $fields)
    {
        if (count($fields) > 0) {
            foreach ($fields as $key => $value) {
                $this->addCols($key, $value);
            }
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

    public function addElement($element)
    {
        $row = $this->getGrid()->addRow();
        $row->addCols([new Col($element)]);
        return $this;
    }

    //Todo Parece que facilita o uso mas não ter padrão de preenchimento dificulta mto
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
        $params = (count($args) == 1) ? func_get_arg(0) : func_get_args();

        if (count($params) == 1) {
            $rows_columns[0] = $params;
        } else {
            $rows_columns = $params;
        }

        $has_visible_field = $this->hasVisibleField($rows_columns);

        $columns = array();
        foreach ($rows_columns as $key => $column) {
            $columnElement = $this->createColumnElement($column);
            $columnClass = (is_array($column) and isset($column[2])) ? $column[2] : null;//Todo refactor after tests
            $columnClass = $column[2] ?? null;
            $columnStyle = (is_array($column) and isset($column[3])) ? $column[3] : null;
            $columnStyle = $column[3] ?? null;
            $gridColumn = new Col($columnElement, $columnClass, $columnStyle);
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

    private function hasVisibleField($fields)
    {
        foreach ($fields as $field) {
            if (!empty($field) and !is_a($field, THidden::class) and !is_a($field, TLabel::class)) {
                return true;
            }
        }
        return false;
    }

    private function needCreateLine($columns)
    {
        if (count($columns) == 0) {
            return false;
        }

        foreach ($columns as $column) {
            /**@var Col $element*/
            $element =$column->getChilds(0);
            if (!is_a($element, THidden::class)) {
                return true;
            }
        }
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
        return $this->grid = $this->grid ?? new DGridBootstrap();
    }

    public function show()
    {
        $this->tpanel = new TPanelGroup($this->title);
        $this->tpanel->class .= ' dvi';
        $this->tpanel->style = 'margin-bottom:10px';

        $this->tpanel->add($this->form);

        $this->addFooterItem($this->group_actions);

        $item = $this->getFooterBoxItems();
        if ($item) {
            $this->tpanel->addFooter($item);
        }
        $this->tpanel->show();
    }

    public function addDVBox(array $param_columns)
    {
        $columns = self::getDVBoxColumns($param_columns);

        $this->addRow($columns);

        return $this;
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

    public function addHBox()
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

    public function addFooterItem($item)
    {
        $this->hboxButtonsFooter->add($item);

        return $this;
    }

    //Todo remove nao parece ser necessário
    protected function getWhiteList(): array
    {
        $whiteList = [
            THidden::class,
            DHidden::class,
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
            DDateTime::class,
            THidden::class,
            THtmlEditor::class,
            DHtmlEditor::class,
            TMultiField::class,
            TFile::class,
            TMultiFile::class,
            TPassword::class,
            DPassword::class,
            TRadioGroup::class,
            DRadioGroup::class,
            TSeekButton::class,
            TDBSeekButton::class,
            TDBCombo::class,
            TSelect::class,
            TSlider::class,
            TSpinner::class,
            DSpinner::class,
            TNumeric::class,
            DNumeric::class,
            TText::class,
            DText::class,
            DCKEditor::class
        ];
        return $whiteList;
    }

    protected function getFooterBoxItems()
    {
        $childs = $this->hboxButtonsFooter->getChilds();
        if (count($childs) > 0) {
            return $this->hboxButtonsFooter;
        }
    }
}
