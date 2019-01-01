<?php

namespace Dvi\Adianti\Widget\Bootstrap\Component;

use Adianti\Base\Lib\Widget\Base\TElement;
use Dvi\Adianti\Widget\Util\Action;
use Dvi\Adianti\Widget\Util\ActionLink;

/**
 * Component DActionGroup
 *
 * @package    Component
 * @subpackage Bootstrap
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class ActionGroup
{
    protected $actionHeader;
    protected $items = array();
    protected $title_group_action;
    protected $icon_size;

    public function __construct($form, $action_header = null, string $title_group_action = null)
    {
        $this->form_default = $form;
        $this->actionHeader = $action_header;
        $this->title_group_action = $title_group_action;

        $this->icon_size = 'fa-2x';
//        parent::__construct();
    }

    public function addAction($action)
    {
        $this->items[] = $action;
    }

    public function addSeparator()
    {
        $this->items[] = '<li role="separator" class="divider"></li>';
    }

    public function addLink(string $route, $icon = null, $label = null, array $parameters = null, $style = null):ActionLink
    {
        if ($icon) {
            $class_icon = explode(' ', $icon);
            unset($class_icon[0]);
            $class_icon = implode(' ', $class_icon);
            $pos = strpos($class_icon, 'fa');
            if ($pos === false or count($class_icon) == 1 and $this->icon_size) {
                $icon .= ' '.$this->icon_size;
            }
        }
        $link = new ActionLink(new Action($route, 'GET', $parameters), $label, $icon);
//Todo check ->        $link->class = 'btn btn-default dvi_panel_action';

        $this->items[] = $link;

        $link->class = 'dvi_btn dvi_group_action_popup_label';

        return $link;
    }

    public function show()
    {
        $group = new TElement('div');
        $group->class="btn-group dropup";

        if ($this->actionHeader) {
            $group->add($this->actionHeader);
        }

        $toggle = '<button type="button" class="btn btn-default dropdown-toggle dvi_panel_action" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            '.$this->title_group_action.'
		    <span class="caret"></span>
		    <span class="sr-only">Toggle Dropdown</span>
		  </button>';
        $group->add($toggle);

        $ul = new TElement('ul');
        $ul->class = "dropdown-menu";
        foreach ($this->items as $action) {
                $ul->add('<li>'.$action.'</li>');
        }
        $group->add($ul);

        return $group->show();
    }
}
