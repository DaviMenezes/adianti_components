<?php

namespace Dvi\Adianti\Widget\Bootstrap\Component;

use Adianti\Base\Lib\Widget\Base\TElement;
use Dvi\Adianti\Widget\Base\GroupField;
use Dvi\Adianti\Widget\Form\DButton;
use Dvi\Adianti\Widget\IDviWidget;
use Dvi\Adianti\Widget\Util\DActionLink;

/**
 * Component InputGroup
 *
 * @version    Dvi 1.0
 * @package    Component
 * @subpackage Bootstrap
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class InputGroup extends GroupField implements IDviWidget
{
    private $inputs = array();
    private $buttons = array();
    private $links = array();

    public function addInput($entry)
    {
        $this->inputs[] = $entry;

        $this->addChilds($entry);
    }

    public function addButton(DButton $button)
    {
        $button->addStyleClass('dvi_form_comp');
        $this->buttons[] = $button;
        $this->addChilds($button);
    }

    public function addLink(DActionLink $link)
    {
        $this->links[] = $link;
        $this->addChilds($link);
    }

    public function show()
    {
        $input_group = new TElement('div');
        $input_group->class= 'input-group';

        $columns = array();
        foreach ($this->inputs as $input) {
            $input_group->add($input);
        }

        $input_group_btn = new TElement('div');
        $input_group_btn->class= 'input-group-btn';

        foreach ($this->buttons as $item) {
            $input_group_btn->add($item);
        }

        $input_group->add($input_group_btn);
        $input_group->show();
    }
}
