<?php
namespace Dvi\Adianti\Widget\Base;

use Adianti\Base\Lib\Widget\Base\TElement;

/**
 * Manipulation to the bootstraps grids
 *
 * @package    grid bootstrap
 * @subpackage base
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class GridBootstrap
{
    protected $grid;
    protected $defaultColClass;
    protected $defaultColStyle;
    protected $rows = array();

    public function __construct($defaultColClass = null, $colStyle = null)
    {
        $this->grid = new TElement('div');

        //$this->grid->style = 'margin-left: 15px; margin-right: 15px;';
        //$this->setContainerDefault();
        $this->defaultColClass = $defaultColClass;
        $this->defaultColStyle = $colStyle;
    }

    public function setContainerFluid()
    {
        $this->grid->class = 'container-fluid';
    }

    public function setContainerDefault()
    {
        $this->grid->class = 'container';
    }

    public function addRow(string $rowStyle = null): GridRow
    {
        $row = new GridRow($rowStyle, $this->defaultColClass, $this->defaultColStyle);
        $this->grid->add($row);

        $this->rows[] = $row;

        return $row;
    }

    public function getRows()
    {
        return $this->rows;
    }

    public function setStyle($style)
    {
        $this->grid->style = $style;
    }

    public function show()
    {
        $this->grid->show();
    }
}
