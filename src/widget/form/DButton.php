<?php

namespace Dvi\Adianti\Widget\Form;

use Adianti\Control\TAction;
use Adianti\Core\AdiantiCoreTranslator;
use Adianti\Widget\Base\TElement;
use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TButton;
use Adianti\Widget\Form\TField;
use Adianti\Widget\Form\TLabel;
use Adianti\Widget\Util\TImage;
use Exception;

/**
 * Form DButton
 *
 * @version    Dvi 1.0
 * @package    Form
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DButton extends TField implements AdiantiWidgetInterface
{
    protected static $class;

    protected $action;
    protected $image;
    protected $properties;
    protected $functions;
    protected $label;
    protected $formName;

    public function __construct($name)
    {
        parent::__construct($name);

        $this->setClass();
    }

    public static function create($name, $callback, $image, $label = null)
    {
        $button = new TButton( $name );
        $button->setAction(new TAction( $callback ), $label);
        $button->setImage( $image );
        $button->setProperty('class', 'btn btn-default btn-sm dvi_btn');
        return $button;
    }

    private function setClass()
    {
        self::$class = '';
    }

    // TButton methods
    public function addStyleClass($class)
    {
        $this->{'class'} = 'btn btn-default '. $class;
    }

    public function setAction(TAction $action, $label = NULL)
    {
        $this->action = $action;
        $this->label  = $label;
    }

    public function getAction():TAction
    {
        return $this->action;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function addFunction($function)
    {
        if ($function)
        {
            $this->functions = $function.';';
        }
    }

    public function setProperty($name, $value, $replace = TRUE)
    {
        $this->properties[$name] = $value;
    }

    public function getProperty($name)
    {
        return $this->properties[$name];
    }

    public static function enableField($form_name, $field)
    {
        TScript::create( " tbutton_enable_field('{$form_name}', '{$field}'); " );
    }

    public static function disableField($form_name, $field)
    {
        TScript::create( " tbutton_disable_field('{$form_name}', '{$field}'); " );
    }

    public function show()
    {
        if ($this->action)
        {
            if (empty($this->formName))
            {
                $label = ($this->label instanceof TLabel) ? $this->label->getValue() : $this->label;
                throw new Exception(AdiantiCoreTranslator::translate('You must pass the ^1 (^2) as a parameter to ^3', __CLASS__, $label, 'TForm::setFields()') );
            }

            // get the action as URL
            $url = $this->action->serialize(FALSE);
            if ($this->action->isStatic())
            {
                $url .= '&static=1';
            }
            $wait_message = AdiantiCoreTranslator::translate('Loading');
            // define the button's action (ajax post)
            $action = "Adianti.waitMessage = '$wait_message';";
            $action.= "{$this->functions}";
            $action.= "__adianti_post_data('{$this->formName}', '{$url}');";
            $action.= "return false;";

            $button = new TElement('button');
            $button->{'id'}      = 'tbutton_'.$this->name;
            $button->{'name'}    = $this->name;
            $button->{'class'}   = 'btn btn-default btn-sm';
            $button->{'onclick'} = $action;
            $action = '';
        }
        else
        {
            $action = $this->functions;
            // creates the button using a div
            $button = new TElement('div');
            $button->{'id'}      = 'tbutton_'.$this->name;
            $button->{'name'}    = $this->name;
            $button->{'class'}   = 'btn btn-default btn-sm';
            $button->{'onclick'} = $action;
        }

        if ($this->properties)
        {
            foreach ($this->properties as $property => $value)
            {
                $button->$property = $value;
            }
        }

        $span = new TElement('span');
        if ($this->image)
        {
            $image = new TElement('span');

            if (substr($this->image,0,3) == 'bs:')
            {
                $image = new TElement('i');
                $image->{'class'} = 'glyphicon glyphicon-'.substr($this->image,3);
            }
            else if (substr($this->image,0,3) == 'fa:')
            {
                $fa_class = substr($this->image,3);
                if (strstr($this->image, '#') !== FALSE)
                {
                    $pieces = explode('#', $fa_class);
                    $fa_class = $pieces[0];
                    $fa_color = $pieces[1];
                }
                $image = new TElement('i');
                $image->{'class'} = 'fa fa-'.$fa_class;
                if (isset($fa_color))
                {
                    $image->{'style'} .= "; color: #{$fa_color}";
                }
            }
            else if (file_exists('app/images/'.$this->image))
            {
                $image = new TImage('app/images/'.$this->image);
            }
            else if (file_exists('lib/adianti/images/'.$this->image))
            {
                $image = new TImage('lib/adianti/images/'.$this->image);
            }
            $image->{'style'} .= '; float:left';
            $span->add($image);
        }

        if ($this->label)
        {
            $span->add(' '. $this->label);
        }
        $button->add($span);
        $button->show();
    }
}