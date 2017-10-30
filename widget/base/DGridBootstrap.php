<?php
namespace Dvi\Widget\Base;

use Adianti\Widget\Base\TElement;
use Dvi\Widget\Base\DGridRow;

/**
 * Manipulation to the bootstraps grids
 *
 * @version    Adianti 4.0
 * @package    grid bootstrap
 * @subpackage base
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DGridBootstrap
{
    private $tnotebookPage;
    private $grid;
    private $defaultColClass;
    private $defaultColStyle;
    private $rows = array();

    public function __construct($defaultColClass = null, $colStyle = null)
    {
        $this->grid = new TElement('div');
//        $this->grid->style = 'margin-left: 15px; margin-right: 15px;';
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
    public function addRow(string $rowStyle = null): DGridRow
    {
        $row = new DGridRow($rowStyle, $this->defaultColClass, $this->defaultColStyle);
        $this->grid->add($row);

        $this->rows[] = $row;

        return $row;
    }

    public function show()
    {
//        foreach ($this->rows as $row) {
//            /**@var DGridRow $row */
//            $row->prepareColumns();
//        }
        $this->grid->show();
    }
}
