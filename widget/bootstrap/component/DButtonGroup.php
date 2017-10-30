<?php
namespace Dvi\Widget\Bootstrap\Component;

use Adianti\Widget\Base\TElement;
use Dvi\Widget\IDviWidget;

/**
 * Model DButtonGroup
 *
 * @version    Dvi 1.0
 * @package    bootstrap
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DButtonGroup implements IDviWidget
{

    private static $items = array();

    public function add($form_name, array $action, $icon = null, array $parameters = null, $label = null)
    {
        $params ='';
        if ($parameters) {
            foreach ($parameters as $key => $value) {
                $params .= '&'.$key.'='.$value;
            }
        }
        $function_post_data = '__adianti_post_data(\''.$form_name.'\', \'class='.get_class($action[0]).'&method='.$action[1].'\');';
        $onclick_btn = 'onclick="Adianti.waitMessage = \'Carregando\'; '.$function_post_data.' return false;";';
        $btn = '<button type="button" class="btn btn-default " '.$onclick_btn.'>';
        if ($icon) {
            $icon = str_replace(':', '-', $icon);
            $btn .= '<li class="fa '.$icon.'"></li>';
        }
        $btn .= ($icon ? ' ': '').$label;
        $btn .= '</button>';

        self::$items[] = $btn;
    }

    public function addLink(array $action, $icon = null, array $parameters = null, $label = null)
    {
        $params ='';
        if ($parameters) {
            foreach ($parameters as $key => $value) {
                $params .= '&'.$key.'='.$value;
            }
        }
        $onclick_link = 'onclick="Adianti.waitMessage = \'Carregando\'; return false;";';
        $link = '<a href="index.php?class='.get_class($action[0]).'&method='.$action[1].$params.'" class="btn btn-default " generator="adianti" '.$onclick_link.'>';
        if ($icon) {
            $icon = str_replace(':', '-', $icon);
            $link .= '<li class="fa '.$icon.'"></li>';
        }
        $link .= ($icon ? ' ': '').$label;
        $link .= '</a>';

        self::$items[] = $link;
    }

    public function show()
    {
        $group = new TElement('div');
        $group->class= 'btn-group';
        $group->role ="group";
        $group->{'aria-label'}="...";
        foreach (self::$items as $item) {
            $group->add($item);
        }

        $group->show();
    }
}