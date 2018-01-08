<?php

namespace Dvi\Adianti\Widget\Form;

use Adianti\Control\TAction;
use Adianti\Widget\Form\TButton;

/**
 * Form DButton
 *
 * @version    Dvi 1.0
 * @package    Form
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DButton extends TButton
{
    public function getAction():TAction
    {
        return parent::getAction();
    }
}