<?php
namespace Dvi\Adianti\Widget\Bootstrap\Component;

use Adianti\Base\Lib\Widget\Base\TElement;
use Dvi\Adianti\Route;
use Dvi\Adianti\Widget\IDviWidget;

/**
 * Model DButtonGroup
 *
 * @version    Dvi 1.0
 * @package    bootstrap
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DButtonGroup implements IDviWidget
{
    private $items = array();
    private $style;

    public function add($form_name, array $action, $icon = null, array $parameters = null, $label = null, $style = null)
    {
        $params ='';
        if ($parameters) {
            foreach ($parameters as $key => $value) {
                $params .= '&'.$key.'='.$value;
            }
        }
        $style = 'style="'.$style.'"';
        $class = Route::getClassName(get_class($action[0]));
        $function_post_data = '__adianti_post_data(\''.$form_name.'\', \'class='. $class .'&method='.$action[1].'\');';
        $onclick_btn = 'onclick="Adianti.waitMessage = \'Carregando\'; '.$function_post_data.' return false;";';
        $btn = '<button class="btn btn-default dvi_btn '.$style.' '.$onclick_btn.'>';
        if ($icon) {
            $icon = str_replace(':', '-', $icon);
            $btn .= '<li class="fa '.$icon.'"></li>';
        }
        $btn .= ($icon ? ' ': '').$label;
        $btn .= '</button>';

        $this->items[] = $btn;
    }

    public function addLink(array $action, $icon = null, array $parameters = null, $label = null, $style = null)
    {
        $params ='';
        if ($parameters) {
            foreach ($parameters as $key => $value) {
                $params .= '&'.$key.'='.$value;
            }
        }

        $style = 'style="'.$style.'"';
        $class_name = Route::getClassName(get_class($action[0]));

        $onclick_link = 'onclick="Adianti.waitMessage = \'Carregando\'; return false;";';
        $href = 'href="index.php?class='.$class_name.'&method='.$action[1].$params. '"';
        $class = 'class="btn btn-default " generator="adianti"';

        $link = '<a '.$href.' '.$class.' '.$style.' '.$onclick_link.'>';
        if ($icon) {
            $icon = str_replace(':', '-', $icon);
            $link .= '<li class="fa '.$icon.'" style="vertical-align:-webkit-baseline-middle;"></li>';
        }
        $link .= ($icon ? ' ': '').$label;
        $link .= '</a>';

        $this->items[] = $link;
    }

    public function setStyle($style)
    {
        $this->style = $style;
    }

    public function show()
    {
        $group = new TElement('div');
        $group->class= 'btn-group';
        $group->role ="group";
        $group->{'aria-label'}="...";
        $group->style = $this->style;
        foreach ($this->items as $item) {
            $group->add($item);
        }

        $group->show();
    }
}
