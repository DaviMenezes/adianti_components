<?php

namespace Dvi\Adianti\Widget\Form\PanelGroup;

use Adianti\Base\Lib\Widget\Container\THBox;
use Adianti\Base\Lib\Widget\Container\TPanelGroup;
use Adianti\Base\Lib\Widget\Form\TForm;
use Adianti\Base\Lib\Widget\Form\THidden;
use Adianti\Base\Lib\Widget\Form\TLabel;
use App\Http\Router;
use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\Widget\Base\GridBootstrap;
use Dvi\Adianti\Widget\Base\GridColumn as Col;
use Dvi\Adianti\Widget\Base\GridElement;
use Dvi\Adianti\Widget\Bootstrap\Component\ButtonGroup;
use Dvi\Adianti\Widget\Container\HBox;
use Dvi\Adianti\Widget\Container\VBox;
use Dvi\Adianti\Widget\IDviWidget;
use Dvi\AdiantiExtension\Route;
use ReflectionClass;
use Stringy\StaticStringy;
use Stringy\Stringy;

/**
 *  PanelGroup
 * @package    Container
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class PanelGroup implements IDviWidget
{
    protected $route_name;

    protected $tpanel;
    protected $grid;
    /**@var TForm */
    protected $form;
    /**@var HBox */
    protected $hboxButtonsFooter;
    /**@var ButtonGroup */
    protected $group_actions;
    protected $form_data;

    protected $useLabelFields = false;
    protected $footer_items = array();
    protected $footer_item;
    protected $title;

    use PanelGroupActionsFacade;
    use PanelGroupFormFacade;
    use PanelGroupNotebookFacade;

    public function __construct(string $route, string $title = null, string $formName = null)
    {
        $this->route_name = $route;

        $form_name =  Router::getShortClassNameByRoute($route). '_'.($formName ?? uniqid());
        $this->form = new TForm((string)$form_name);
        $this->form->class = 'form-horizontal';
        $this->form->add($this->getGrid());

        $this->title = $title;

        $this->hboxButtonsFooter = new HBox;

        $this->group_actions = new ButtonGroup($this->form);
    }

    public function setTitle($title)
    {
        $this->title = trim($title);
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public static function create($route, string $title = null, string $formName = null)
    {
        $obj = new PanelGroup($route, $title, $formName);
        return $obj;
    }

    public function addGroupButton(array $buttons)
    {
        $group = new ButtonGroup();
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
            $columnClass = (is_array($column) and isset($column[2])) ? $column[2] : null;
            $columnClass = $column[2] ?? null;
            $columnStyle = (is_array($column) and isset($column[3])) ? $column[3] : null;
            $columnStyle = $column[3] ?? null;
            $gridColumn = new Col($columnElement, $columnClass, $columnStyle);
            $element = $columnElement->getElement();
            $this->addFormField($element);

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
            /**@var Col $element */
            $element = $column->getChilds(0);
            if (!is_a($element, THidden::class)) {
                return true;
            }
        }
        return false;
    }

    private function createColumnElement($field): GridElement
    {
        if (is_array($field)) {
            $gridElement = new GridElement($field[0]);
        } else {
            $gridElement = new GridElement($field);
        }
        return $gridElement;
    }

    public function getGrid(): GridBootstrap
    {
        return $this->grid = $this->grid ?? new GridBootstrap();
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
            $dvbox = new VBox();
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

    protected function getFooterBoxItems()
    {
        $childs = $this->hboxButtonsFooter->getChilds();
        if (count($childs) > 0) {
            return $this->hboxButtonsFooter;
        }
    }
}
