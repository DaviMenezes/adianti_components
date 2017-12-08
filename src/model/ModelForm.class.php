<?php

namespace Dvi\Adianti\Model;

use Dvi\Adianti\Widget\Base\DGridColumn;
use FontLib\Table\Type\name;

/**
 * Model ModelForm
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Adianti Components
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait ModelForm
{
    private $form_rows = array();

    private function setTypeText(string $name,  int $size, bool $required = false, $label = null)
    {
        $model = get_called_class();
        $this->$name = new DBFieldText($name, 'text', $size, $required, $label);
    }

    private function setStructureForm($rows)
    {
        foreach ($rows as $key => $row) {
            $cols = array();
            foreach ($row as $column) {
                $cols[] = new DGridColumn($column->getFormField());
            }
            $rows[$key] = $cols;
        }

        $this->form_rows = $rows;
    }

    public function getFormRows()
    {
        return $this->form_rows;
    }
}