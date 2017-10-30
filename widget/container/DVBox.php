<?php
namespace Dvi\Widget\Container;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Container\TVBox;

/**
 * Coluna bootstraps
 *
 * @version    Dvi 1.0
 * @package    grid bootstrap
 * @subpackage base
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DVBox extends TVBox
{
    private $childs;

    public function __construct()
    {
        parent::__construct();
    }
    
    public static function pack()
    {
        $box = new self;
        $box->{'style'} = 'display:block; ';
        $args = func_get_args();
        if ($args) {
            foreach ($args as $arg) {
                $box->addElement($arg);
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
        $this->childs[] = $child;
        return parent::add($child, $style);
    }

    public function getChilds($position = null)
    {
        if ($position) {
            return $this->childs[$position];
        } else {
            return $this->childs;
        }
    }
}
