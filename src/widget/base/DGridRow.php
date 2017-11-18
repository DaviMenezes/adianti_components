<?php
namespace Dvi\Adianti\Widget\Base;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Form\TLabel;

/**
 * Row to bootstrap grid
 *
 * @version    Adianti 4.0
 * @package    grid bootstrap
 * @subpackage base
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DGridRow extends TElement
{
    private $childStyle;
    private $defaultColClass;
    private $defaultColStyle;
    private $columns = array();
    private $maxColumns = 12;


    private $bootstrapClassDefault;
    private $defaultColType;

    public function __construct($rowStyle, $defaultClass = null, $colStyle = null)
    {
        parent::__construct('div');
        $this->class = 'row';
        $this->{'style'} = 'margin-left: -5px; ';
//        $this->{'style'} = 'margin-right: 15px; ';
        $this->{'style'} .= 'clear:both; ';
        $this->style .= $rowStyle;

        $this->defaultColType = 'md';
        $this->defaultColClass = $defaultClass ?? $this->defaultColType.'-6';
        $this->defaultColStyle = $colStyle;
        $this->childStyle = '';
    }

    public function addCol(DGridColumn $column)
    {
        $this->columns[] = $column;
        parent::add($column);

        return $this;
    }


    public function addCols()
    {
        $array_columns = (count(func_get_args()) == 1) ? func_get_arg(0) : func_get_args();

//        $class = $this->getBootstrapColumnClass($array_columns);

        foreach ($array_columns as $position => $column) {
            if (!is_a($column, 'Dvi\Widget\Base\DGridColumn')) {
//                $gridColumn = $this->createColumn($column, $position, $class);
            }
            parent::add($gridColumn ?? $column);
        }
    }

    private function createColumn($column, $position, $class)
    {
        if (is_array($column)) {
            $gridColumn = new DGridColumn($column[0], $column[1] ?? $class, $column[2] ?? $this->defaultColStyle);
        } else {
            /* @var GridElement $column */
            $column_class = ($this->isInitialLabel($column) && $position == 0) ? 'md-2 control-label' : 'md-'.$class;

            $gridColumn = new DGridColumn($column, $column_class);
        }

        return $gridColumn;
    }
    private function isInitialLabel($param)
    {
        if (is_string($param) || (is_a($param, 'GridElement') || method_exists($param, 'getElement') && is_a($param->getElement(), 'TLabel'))) {
            return true;
        }
    }
    public function getBootstrapColumnClass($columns)
    {
        $qtdColumnsToLabel = 2;
        $qtdColumnsString = 0;

        foreach ($columns as $column) {
            if (is_string($column)) {
                $qtdColumnsString ++;
            }
        }
        $qtdValidFields = count($columns) - $qtdColumnsString;
        $restColumnAvailable = ($this->maxColumns - ($qtdColumnsToLabel * $qtdColumnsString)) / ($qtdValidFields == 0 ? 1 : $qtdValidFields);
        $class = $this->bootstrapClassDefault . floor($restColumnAvailable);
        return $class;
    }
}
