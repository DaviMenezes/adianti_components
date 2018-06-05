<?php

namespace Dvi\Adianti\Widget\Bootstrap\Component;

use Adianti\Base\Lib\Widget\Base\TElement;
use Dvi\Adianti\Control\DAction;
use Dvi\Adianti\Widget\Util\DActionLink;
use Dvi\Adianti\WidgetBootstrap\Component\GroupActions;

/**
 * Component DActionGroup
 *
 * @package    Component
 * @subpackage Bootstrap
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DActionGroup extends GroupActions
{
    private $actionHeader;
    protected $items = array();
    private $title_group_action;

    public function __construct($action_header = null, string $title_group_action = null)
    {
        $this->actionHeader = $action_header;
        $this->title_group_action = $title_group_action;

        parent::__construct();
    }

    public function addAction($action)
    {
        $this->items[] = $action;
    }

    public function addSeparator()
    {
        $this->items[] = '<li role="separator" class="divider"></li>';
    }

    public function addLink(array $callback, $icon = null, $label = null, array $parameters = null, $style = null):DActionLink
    {
//        $link = new DActionLink(new DAction($callback, $parameters, $icon, $label));
//        $link->class = 'dvi_btn';
//
//        $this->currentAction = $link;
//        $this->items[] = $link;

        $link = parent::addLink($callback, $icon, $label, $parameters, $style);
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

        $toggle = '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
