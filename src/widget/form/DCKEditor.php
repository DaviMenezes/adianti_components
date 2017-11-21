<?php
namespace Dvi\Adianti\Widget\Form;

use Adianti\Widget\Base\TElement;
use Adianti\Widget\Base\TScript;
use Adianti\Widget\Form\AdiantiWidgetInterface;
use Adianti\Widget\Form\TField;

/**
 * Html Editor - DCKEditor
 *

 * @version    1.0
 * @package    widget
 * @subpackage form
 * @author     Davi Menezes
 */
class DCKEditor extends TField implements AdiantiWidgetInterface
{
    protected $id;
    protected $name;
    protected $value;
    protected $tag;
    protected $size;
    protected $height;

    /**
     * Class Constructor
     * @param $name Widet's name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        
        //$identy = 'dckeditor_'.mt_rand(1000000000, 1999999999);
        $this->id = $name;
        // creates a tag
        $this->tag = new TElement('textarea');
    }
    
    /**
     * Define the widget's size
     * @param  $width   Widget's width
     * @param  $height  Widget's height
     */
    public function setSize($width, $height = null)
    {
        $this->size   = $width;
        if ($height) {
            $this->height = $height;
        }
    }
    
    /**
     * Define the field's value
     * @param $value string string containing the field's value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    
    
    /**
     * Show the widget
     */
    public function show()
    {

        // add the content to the textarea
        $this->tag->{'id'} = $this->id;
        $this->tag->{'name'} = $this->name;
        $this->tag->add(htmlspecialchars($this->value));
        // show the tag
        $this->tag->show();
        
        TScript::create(" CKEDITOR.replace( '{$this->tag->{'id'}}');");
    }

    public function setName($name)
    {
        // TODO: Implement setName() method.
    }

    public function getName()
    {
        // TODO: Implement getName() method.
    }

    public function getValue()
    {
        return $this->value;
    }
}
