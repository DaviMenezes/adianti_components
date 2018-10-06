<?php

namespace Dvi\Adianti\Widget\Container;

use Adianti\Base\Lib\Widget\Base\TElement;
use Adianti\Base\Lib\Widget\Container\THBox;
use Dvi\Adianti\Widget\IGroupField;

/**
 * Extension of the THBox. Adiciona alguns botões com estilo alterado
 *
 * @package    grid bootstrap
 * @subpackage base
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class HBox extends THBox implements IGroupField
{
    protected $childs = array();

    public function __construct()
    {
        parent::__construct();
    }

    public function addButton($child, $style = 'display:inline-table; ')
    {
        $this->childs[] = $child;

        $wrapper = new TElement('div');
        $wrapper->{'style'} = $style;
        $wrapper->class = 'dvi_btn';
        $wrapper->add($child);
        parent::add($wrapper);
        return $wrapper;
    }

    public function addGroupRightButton(array $childs)
    {
        foreach ($childs as $key => $button) {
            $style = 'min-height:36px; ';
            if (count($childs) == 1) {
                $style .= 'border-radius: 0 3px 3px 0; margin-left:-1px;';
            } elseif ($key == 0) {
                $style .= 'border-radius: 0px; margin-left:-1px; margin-right:-5px';
            } elseif (($key + 1) < count($childs)) {
                $style .= 'border-radius:0px; margin-right:-5px';
            } else {
                $style .= 'border-radius: 0 3px 3px 0;';
            }

            $button->style = $style;
            
            $this->addButton($button);
        }
    }

    /* Static method for pack content
     * @param $cells Each argument is a cell
     */
    public static function buttonPack()
    {
        $box = new self;
        $args = func_get_args();
        if ($args) {
            foreach ($args as $arg) {
                $box->addButton($arg);
            }
        }
        return $box;
    }

    public function add($child, $style = 'display:inline-table')
    {
        $child->{'style'} = $style;

        $this->childs[] = $child;

        $this->children[] = $child;

        if ($child instanceof TElement) {
            $child->setIsWrapped(true);
        }
        return $child;
    }

    public function getChilds($position = null): array
    {
        if ($position) {
            return $this->childs[$position];
        } else {
            return $this->childs;
        }
    }
}
