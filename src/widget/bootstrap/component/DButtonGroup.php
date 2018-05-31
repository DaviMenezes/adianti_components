<?php
namespace Dvi\Adianti\Widget\Bootstrap\Component;

use Adianti\Base\Lib\Widget\Base\TElement;
use Dvi\Adianti\Widget\IDviWidget;
use Dvi\Adianti\WidgetBootstrap\Component\GroupActions;

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
class DButtonGroup extends GroupActions implements IDviWidget
{
    protected $style;
    protected $form_default;
    public $current_group;
    protected $class;

    private $group;

    public function __construct($default_form = null)
    {
        $this->form_default = $default_form;
        parent::__construct();
        $this->class = 'btn-group';
    }

    public function setClass($class)
    {
        $this->class .= ';'.$class;
    }

    public function addGroup($action_header = null, $title_group_action = null): DActionGroup
    {
        $this->group = new DActionGroup($action_header, $title_group_action);
        $this->items[] = $this->group;
        return $this->group;
    }

    public function show()
    {
        $group = new TElement('div');
        $group->class= $this->class;
        $group->role ="group";
        $group->{'aria-label'}="...";
        $group->style = $this->style;

        foreach ($this->items as $item) {
            $group->add($item);
        }

        return $group->show();
    }
}
