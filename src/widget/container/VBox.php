<?php
namespace Dvi\Adianti\Widget\Container;

use Adianti\Base\Lib\Widget\Base\TElement;
use Adianti\Base\Lib\Widget\Container\TVBox;
use Dvi\Adianti\Widget\IGroupField;

/**
 * Coluna bootstraps
 * @package    grid bootstrap
 * @subpackage base
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class VBox extends TVBox implements IGroupField
{
    protected $childs;

    public function __construct()
    {
        parent::__construct();

        $this->style = 'width:100%';
    }
    
    public static function pack()
    {
        $box = new self;
        $box->{'style'} = 'display:block; ';
        $args = func_get_args();
        if ($args) {
            foreach ($args as $arg) {
                $box->add($arg);
            }
        }
        return $box;
    }
    
    public function addElement($child, $style = 'display:block; ')
    {
        $wrapper = new TElement('div');
        $wrapper->{'style'} = $style;
        $wrapper->add($child);
        parent::add($wrapper);
        return $wrapper;
    }

    public function add($child, $style = null)
    {
        if ($style) {
            $child->{'style'} .= $style;
        }
        $this->childs[] = $child;
        return parent::add($child);
    }

    public function getChilds($position = null):array
    {
        if ($position) {
            return $this->childs[$position];
        } else {
            return $this->childs;
        }
    }
}
