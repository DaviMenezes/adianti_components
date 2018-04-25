<?php

namespace Dvi\Adianti\Widget\Datagrid;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Core\AdiantiCoreTranslator;
use Adianti\Base\Lib\Widget\Base\TElement;
use Adianti\Base\Lib\Widget\Datagrid\TPageNavigation;

/**
 * DPageNavigation
 *
 * @version    Dvi 1.0
 * @package    pacageTeste
 * @subpackage subPackageTeste
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DPageNavigation extends TPageNavigation
{
    protected $hidden;
    protected $action;
    protected $first_page;
    protected $limit;
    protected $count;
    protected $direction;
    protected $order;
    protected $page;
    protected $width;

    public function show()
    {
        if ($this->hidden) {
            return;
        }

        if (!$this->action instanceof TAction) {
            throw new \Exception(AdiantiCoreTranslator::translate('You must call ^1 before add this component', __CLASS__ . '::' . 'setAction()'));
        }

        $first_page  = isset($this->first_page) ? $this->first_page : 1;
        $direction   = 'asc';
        $page_size = isset($this->limit) ? $this->limit : 10;
        $max = 10;
        $registros = $this->count;

        if (!$registros) {
            $registros = 0;
        }

        if ($page_size > 0) {
            $pages = (int) ($registros / $page_size) - $first_page +1;
        } else {
            $pages = 1;
        }

        if ($page_size>0) {
            $resto = $registros % $page_size;
        }

        $pages += $resto>0 ? 1 : 0;
        $last_page = min($pages, $max);

        $nav = new TElement('div');
        $nav->{'class'} = 'dpagenavigation';
        $nav->align = 'center';

        $ul = new TElement('div');
        $ul->{'class'} = 'pagination row';
        $nav->add($ul);

        // previous
        $item = new TElement('div');
        $item->{'class'} = 'col-xs-1  btn btn-default';
        $link = new TElement('a');

        $a_div = new TElement('div');
        $link->{'href'} = '#';
        $link->{'aria-label'} = 'Previous';
        $ul->add($item);
        $item->add($link);
        $link->add($a_div);

        if ($first_page > 1) {
            $this->action->setParameter('offset', ($first_page - $max -1) * $page_size);
            $this->action->setParameter('limit', $page_size);
            $this->action->setParameter('direction', $this->direction);
            $this->action->setParameter('page', $first_page - $max);
            $this->action->setParameter('first_page', $first_page - $max);
            $this->action->setParameter('order', $this->order);


            $link->href      = $this->action->serialize();
            $link->generator = 'adianti';
            $a_div->add('<span><i class="fa fa-angle-left fa-3x" aria-hidden="true"></i></span>');
        } else {
            $a_div->add('<span>&nbsp;</span>');
        }
        $item->{'class'} .= ' pagination_item item_left';

        for ($n = $first_page; $n <= $last_page + $first_page -1; $n++) {
            $offset = ($n -1) * $page_size;
            $item = new TElement('div');
            $item->{'class'} = 'col-xs-1 btn btn-default pagination_item';
            $link = new TElement('a');

            $a_div = new TElement('div');

            $this->action->setParameter('offset', $offset);
            $this->action->setParameter('limit', $page_size);
            $this->action->setParameter('direction', $this->direction);
            $this->action->setParameter('page', $n);
            $this->action->setParameter('first_page', $first_page);
            $this->action->setParameter('order', $this->order);

            $link->href      = $this->action->serialize();
            $link->generator = 'adianti';

            $ul->add($item);
            $item->add($link);
            $link->add($a_div);
            $a_div->add('<span>'.$n.'</span>');

            if ($this->page == $n) {
                $item->{'class'} = 'col-xs-1 btn btn-primary pagination_item';
            }
        }

        for ($z=$n; $z<=10; $z++) {
            $item = new TElement('div');
            $item->{'class'} = 'col-xs-1 btn btn-default pagination_item disabled';
            $link = new TElement('a');
            $a_div = new TElement('div');
//            $item->{'class'} = 'off';
            $ul->add($item);
            $item->add($link);
            $link->add($a_div);
            $a_div->add('<span>'.$z.'</span>');
        }

        $item = new TElement('div');
        $item->{'class'} = 'col-xs-1 btn btn-default pagination_item';
        $link = new TElement('a');

        $a_div = new TElement('div');
        $item->{'aria-label'} = "Next";
        $ul->add($item);
        $item->add($link);
        $link->add($a_div);

        if ($pages > $max) {
            $offset = ($n -1) * $page_size;
            $first_page = $n;

            $this->action->setParameter('offset', $offset);
            $this->action->setParameter('limit', $page_size);
            $this->action->setParameter('direction', $this->direction);
            $this->action->setParameter('page', $n);
            $this->action->setParameter('first_page', $first_page);
            $this->action->setParameter('order', $this->order);
            $link->href      = $this->action->serialize();
            $link->generator = 'adianti';

            $a_div->add('<span><i class="fa fa-angle-right" aria-hidden="true"></i></span>');
        } else {
            $a_div->add('<span>&nbsp;</span>');
        }
        $item->{'class'} .= ' item_right';
        $nav->show();
    }
}
