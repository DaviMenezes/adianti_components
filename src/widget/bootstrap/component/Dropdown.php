<?php

namespace Dvi\Adianti\Widget\Bootstrap\Component;

use Adianti\Base\Lib\Widget\Base\TElement;

/**
 * Component DviDropdown
 *
 * @package    App
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DviDropdown
{
    private $items = array();
    private $label;
    private $button_id;
    private $menu_align;

    public function __construct($label)
    {
        $this->label = $label;

        $this->setAlignLeft();
    }

    public function addAction($route, $label, $faIcon, array $arrayParams = null)
    {
        $link = ['href' => urlRoute($route, $arrayParams), 'label' => $label, 'icon' => $faIcon];
        $this->addItems($link, 'link');
    }

    public function addEditAction($route, $id)
    {
        $route = urlRoute($route . '/id/' . $id);
        $link = ['href' => $route, 'label' => _t('Edit'), 'icon' => 'fa-pencil-square-o blue fa-2x'];
        $this->addItems($link, 'link');
    }

    public function addDeleteAction($route, $id)
    {
        $route = urlRoute($route . '/id/' . $id);
        $link = ['href' => $route, 'label' => _t('Delete'), 'icon' => 'fa-trash-o red fa-2x'];
        $this->addItems($link, 'link');
    }

    public function addSeparator()
    {
        $li = new TElement('li');
        $li->role = 'separator';
        $li->class = 'divider';
        $el = '<li role="separator" class="divider"></li>';
        $this->addItems($el, 'separator');
    }

    /**
     * Get Html Bootstrap Dropdown
     * @param string $type = ('down or up')
     * @return string
     */
    public function show($type = 'down')
    {
        $element = new TElement('div');
        $element->class = 'drop' . $type;
        $element->add($this->getButton());
        $element->add($this->getActions());

        return $element->getContents();
    }

    public function setButtonId(string $id)
    {
        $this->button_id = $id;
    }

    public function setAlignLeft()
    {
        $this->menu_align = 'left';
    }

    public function setAlignRight()
    {
        $this->menu_align = 'right';
    }

    private function addItems($item, $type)
    {
        $this->items[] = [$type => $item];
    }

    private function getButton()
    {
        $button = new TElement('button');
        $button->class = 'btn btn-default dropdown-toggle';
        $button->type = 'button';
        $button->id = $this->button_id;
        $button->{'data-toggle'} = 'dropdown';
        $button->{'aria-haspopup'} = 'true';
        $button->{'aria-expanded'} = 'true';

        $button_icon_bars = new TElement('i');
        $button_icon_bars->class = 'fa fa-bars';
        $button_icon_bars->{'aria-hidden'} = "true";

        $button->add($button_icon_bars);

        $button->add($this->label);

        $button_icon_caret = new TElement('span');
        $button_icon_caret->class = 'caret';

        $button->add($button_icon_caret);

        return $button;
    }

    private function getActions()
    {
        $ul = new TElement('ul');
        $ul->class = 'dropdown-menu dropdown-menu-' . $this->menu_align;
        $ul->tag('aria-labelledby', $this->button_id);

        foreach ($this->items as $item) {
            if (isset($item['link'])) {
                $link = $item['link'];

                $li = '<li>'
                        . '<a href = ' . $link['href'] . ' generator="adianti" style = "height:30px; cursor:pointer">'
                            . '<i class="fa ' . $link['icon'] . '" aria-hidden="true" style="float:left; width:25px"></i>'
                            . '<div style="font-size:14px">' . $link['label'] . '</div>'
                        . '</a>'
                    . '</li>';

                $ul->add($li);
            } elseif (isset($item['separator'])) {
                $ul->add($item['separator']);
            }
        }

        return $ul;
    }
}
