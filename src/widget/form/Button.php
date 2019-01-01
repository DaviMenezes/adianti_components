<?php

namespace Dvi\Adianti\Widget\Form;

use Adianti\Base\Lib\Control\TAction;
use Adianti\Base\Lib\Core\AdiantiCoreTranslator;
use Adianti\Base\Lib\Widget\Base\TElement;
use Adianti\Base\Lib\Widget\Base\TScript;
use Adianti\Base\Lib\Widget\Form\AdiantiWidgetInterface;
use Adianti\Base\Lib\Widget\Form\TField;
use Adianti\Base\Lib\Widget\Form\TLabel;
use Adianti\Base\Lib\Widget\Util\TImage;
use Dvi\Adianti\Helpers\GUID;
use Dvi\Adianti\Widget\Util\Action;
use Exception;

/**
 * Form Button
 * @package    Form
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class Button extends TField implements AdiantiWidgetInterface
{
    protected static $class;

    /**@var Action*/
    protected $action;
    protected $image;
    protected $properties;
    protected $functions;
    protected $label;
    protected $formName;

    public function __construct(string $name = null)
    {
        parent::__construct($name ?? 'btn_'.GUID::getID());

        $this->setClass();
    }

    /**
     * @param string $name
     * @param string $route
     * @param string $image
     * @param string|null $label
     * @return Button
     * @throws Exception
     */
    public static function create(string $name, string $route, string $image = null, string $label = null)
    {
        $button = new Button($name);

        if ($label) {
            $element_label = new TElement('div');
            $element_label->add($label);
            if ($image) {
                $element_label->class = 'dvi_btn_label';
            }
            $button->setAction(new Action($route, 'POST'), $element_label);
        } else {
            $button->setAction(new Action($route, 'POST'), $label);
        }

        if ($image) {
            $button->icon($image);
        }

        $button->style = 'font-size: 14px;';
        if (!$image) {
            $button->style = 'font-size: 15px;';
        }
        return $button;
    }

    private function setClass()
    {
        self::$class = 'dvi_btn';
    }

    // TButton methods
    public function addStyleClass($class)
    {
        $this->{'class'} = 'btn btn-default '. $class;
    }

    public function setAction(Action $action, $label = null)
    {
        $this->action = $action;
        $this->label  = $label;
    }

    public function getAction():Action
    {
        return $this->action;
    }

    public function icon(string $image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function addFunction($function)
    {
        if ($function) {
            $this->functions = $function.';';
        }
    }

    /**
     * @param string $name
     * @param string $value
     * @param bool $replace
     */
    public function setProperty($name, $value, $replace = true)
    {
        $this->properties[$name] = $value;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getProperty($name)
    {
        return $this->properties[$name];
    }

    /**
     * @param string  $form_name
     * @param string $field
     */
    public static function enableField($form_name, $field)
    {
        TScript::create(" tbutton_enable_field('{$form_name}', '{$field}'); ");
    }

    /**
     * @param string $form_name
     * @param string $field
     */
    public static function disableField($form_name, $field)
    {
        TScript::create(" tbutton_disable_field('{$form_name}', '{$field}'); ");
    }

    public function show()
    {
        if ($this->action) {
            if (empty($this->formName)) {
                $label = ($this->label instanceof TLabel) ? $this->label->getValue() : $this->label;
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $label, 'TForm::setFields()'));
            }

            // get the action as URL
            $route = $this->action->serialize(false);
            if ($this->action->isStatic($route)) {
                $route .= '&static=1';
            }
            //Todo customizar mensagem de carregamento
            $wait_message = AdiantiCoreTranslator::translate('Loading');
            // define the button's action (ajax post)
            $action = "Adianti.waitMessage = '$wait_message';";
            $action.= "{$this->functions}";
            $action.= "__adianti_post_data('{$this->formName}', '{$route}');";
            $action.= "return false;";

            $button = new TElement('button');
            $button->{'id'}      = 'tbutton_'.$this->name;
            $button->{'name'}    = $this->name;
            $button->{'class'}   = 'btn btn-default btn-sm';
            $button->{'onclick'} = $action;
            $action = '';
        } else {
            $action = $this->functions;
            // creates the button using a div
            $button = new TElement('div');
            $button->{'id'}      = 'tbutton_'.$this->name;
            $button->{'name'}    = $this->name;
            $button->{'class'}   = 'btn btn-default btn-sm';
            $button->{'onclick'} = $action;
        }

        if ($this->properties) {
            foreach ($this->properties as $property => $value) {
                $button->$property = $value;
            }
        }

        $span = new TElement('span');
        if ($this->image) {
            $image = new TElement('span');

            if (substr($this->image, 0, 3) == 'bs:') {
                $image = new TElement('i');
                $image->{'class'} = 'glyphicon glyphicon-'.substr($this->image, 3);
            } elseif (substr($this->image, 0, 3) == 'fa:') {
                $fa_class = substr($this->image, 3);
                if (strstr($this->image, '#') !== false) {
                    $pieces = explode('#', $fa_class);
                    $fa_class = $pieces[0];
                    $fa_color = $pieces[1];
                }
                $image = new TElement('i');
                $image->{'class'} = 'fa fa-'.$fa_class;
                if (isset($fa_color)) {
                    $image->{'style'} .= "; color: #{$fa_color}";
                }
            } elseif (file_exists('app/images/'.$this->image)) {
                $image = new TImage('app/images/'.$this->image);
            } elseif (file_exists('lib/adianti/images/'.$this->image)) {
                $image = new TImage('lib/adianti/images/'.$this->image);
            }
            $rpos = strrpos($this->image, 'fa-');
            $has_size = null;
            $span_class = null;
            if ($rpos) {
                $has_size = strrpos(substr($this->image, $rpos), 'x');
                if ($has_size !== false) {
                    $span_class = 'class="align_action_middle"';
                }
            }

            $span->add('<span '.$span_class.'>'.$image.'</span>');
        }

        if ($this->label) {
            $span->add(' '. $this->label);
        }
        $button->add($span);
        $button->show();
    }
}
