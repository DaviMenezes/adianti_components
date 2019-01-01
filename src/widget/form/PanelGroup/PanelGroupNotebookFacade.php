<?php

namespace Dvi\Adianti\Widget\Form\PanelGroup;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Container\TNotebook;
use Adianti\Base\Lib\Widget\Form\TForm;
use Adianti\Base\Lib\Wrapper\BootstrapNotebookWrapper;
use Dvi\Adianti\Widget\Base\GridBootstrap;
use Dvi\Adianti\Widget\Util\Action;

/**
 * Form PanelGroupNotebookFacade
 *
 * @package    Form
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait PanelGroupNotebookFacade
{
    /**@var TNotebook*/
    protected $notebook;
    /**@var TForm*/
    protected $form;

    public function addNotebook()
    {
        $notebook = new TNotebook();
        $this->notebook = new BootstrapNotebookWrapper($notebook);
        $this->form->add($this->notebook);

        return $this;
    }

    public function appendPage(string $title)
    {
        $this->grid = new GridBootstrap();
        $this->getNotebook()->appendPage($title, $this->grid);

        return $this;
    }

    /**@return TNotebook*/
    public function getNotebook()
    {
        if (!$this->notebook) {
            $this->addNotebook();
        }
        return $this->notebook;
    }

    public function setNotebookPageAction($route, array $parameters = null)
    {
        $action = new Action($route, 'GET', $parameters);
        $this->notebook->setTabAction($action);

        return $this;
    }

    public function setCurrentNotebookPage(int $index)
    {
        $this->notebook->setCurrentPage($index);

        return $this;
    }
}
