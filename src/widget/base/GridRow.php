<?php
namespace Dvi\Adianti\Widget\Base;

use Adianti\Base\Lib\Widget\Base\TElement;

/**
 * Row to bootstrap grid
 *
 * @package    grid bootstrap
 * @subpackage base
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class GridRow extends TElement
{
    protected $childStyle;
    protected $defaultColClass;
    protected $defaultColStyle;
    protected $columns = array();
    protected $maxColumns = 12;

    protected $bootstrapClassDefault;
    protected $defaultColType;

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

    public function addCol(GridColumn $column)
    {
        $this->columns[] = $column;
        parent::add($column);

        return $this;
    }

    public function col($element, array $class = null, string $style = null)
    {
        $col = new GridColumn($element, $class, $style);
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
