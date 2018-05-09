<?php
namespace Dvi\Adianti\Widget\Base;

use Adianti\Base\Lib\Widget\Base\TElement;

/**
 * Row to bootstrap grid
 *
 * @version    Adianti 4.0
 * @package    grid bootstrap
 * @subpackage base
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
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

    public function __construct($rowStyle = null, $defaultClass = null, $colStyle = null)
    {
        parent::__construct('div');
        $this->class = 'row';
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

    public function col($element, array $class = null, string $style = null)
    {
        $col = new DGridColumn($element, $class, $style);
        $this->addCol($col);
        return $this;
    }

    public function addCols(array $columns)
    {
        foreach ($columns as $position => $column) {
            $this->columns[] = $column;
            parent::add($column);
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
