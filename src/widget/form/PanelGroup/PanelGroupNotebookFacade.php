<?php

namespace Dvi\Adianti\Widget\Form\PanelGroup;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Widget\Container\TNotebook;
use Adianti\Base\Lib\Widget\Form\TForm;
use Adianti\Base\Lib\Wrapper\BootstrapNotebookWrapper;
use Dvi\Adianti\Widget\Base\DGridBootstrap;

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
    /**@var TNotebook $notebook*/
    protected $notebook;
    /**@var TForm $form*/
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
        $this->grid = new DGridBootstrap();
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

    public function setNotebookPageAction(array $callback, array $parameters = null)
    {
        $this->notebook->setTabAction(new TAction($callback, $parameters));

        return $this;
    }

    public function setCurrentNotebookPage(int $index)
    {
        $this->notebook->setCurrentPage($index);

        return $this;
    }
}
