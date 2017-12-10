<?php
namespace Dvi\Adianti\Widget\Base;

use Adianti\Widget\Form\TLabel;

/**
 * representa um elemento a ser manipulado
 *
 * @version    Adianti 4.0
 * @package    grid bootstrap
 * @subpackage base
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class GridElement
{
    private $element;
    public $insideForm;

    public function __construct($element, $inside_form = 'inside')
    {
        $this->setElement($element);
        $this->insideForm = $inside_form;
    }

    public function setElement($element)
    {
        if (is_string($element)) {
            $this->element = new TLabel($element);
        } else {
            $this->element = $element;
        }
    }

    public function getElement()
    {
        return $this->element;
    }

    public function isInitialLabel()
    {
        if (is_string($this->element) || is_a($this->element, 'TLabel')) {
            return true;
        }
    }

    public function show()
    {
        $this->element->show();
    }
}