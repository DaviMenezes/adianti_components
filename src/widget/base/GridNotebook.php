<?php

namespace Dvi\Adianti\Widget\Base;

class GridNotebook
{
    private $tabs = array();
    private $rows = array();


    public function __construct(string $title = null, array $rows = null)
    {
        $this->tabs[] = $title;
        $this->rows = $rows;
    }

    /**
     * @param array $tabs
     */
    public function setTabs(array $tabs)
    {
        $this->tabs = $tabs;
    }

    /**
     * @return array
     */
    public function getTabs(): array
    {
        return $this->tabs;
    }
    public function addTab(string $title)
    {
        $this->tabs[] = $title;
        return $this;
    }
    public function setRows(array $rows)
    {
        $this->rows = $rows;
    }

    public function addRow(DGridRow $row)
    {
        $this->rows[] = $row;
        return $this;
    }

    public function getRows()
    {
        return $this->rows;
    }
}
