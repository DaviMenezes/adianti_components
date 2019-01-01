<?php

namespace Dvi\Adianti\Widget\Base;

use Adianti\Base\Lib\Widget\Base\TElement;
use Adianti\Base\Lib\Widget\Form\TField;
use Adianti\Base\Lib\Widget\Form\TLabel;
use App\Http\Request;
use Dvi\Adianti\Widget\Container\VBox;
use Dvi\Adianti\Widget\Form\Button;
use Dvi\Adianti\Widget\Form\Field\Contract\FormField;

/**
 * Column to bootstrap grid
 *
 * @package    grid bootstrap
 * @subpackage base
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class GridColumn extends TElement
{
    #region [BootstrapGridClasses]
    const XS1 = 'col-xs-1';
    const XS2 = 'col-xs-2';
    const XS3 = 'col-xs-3';
    const XS4 = 'col-xs-4';
    const XS5 = 'col-xs-5';
    const XS6 = 'col-xs-6';
    const XS7 = 'col-xs-7';
    const XS8 = 'col-xs-8';
    const XS9 = 'col-xs-9';
    const XS10 = 'col-xs-10';
    const XS11 = 'col-xs-11';
    const XS12 = 'col-xs-12';
    const SM1 = 'col-sm-1';
    const SM2 = 'col-sm-2';
    const SM3 = 'col-sm-3';
    const SM4 = 'col-sm-4';
    const SM5 = 'col-sm-5';
    const SM6 = 'col-sm-6';
    const SM7 = 'col-sm-7';
    const SM8 = 'col-sm-8';
    const SM9 = 'col-sm-9';
    const SM10 = 'col-sm-10';
    const SM11 = 'col-sm-11';
    const SM12 = 'col-sm-12';
    const MD1 = 'col-md-1';
    const MD2 = 'col-md-2';
    const MD3 = 'col-md-3';
    const MD4 = 'col-md-4';
    const MD5 = 'col-md-5';
    const MD6 = 'col-md-6';
    const MD7 = 'col-md-7';
    const MD8 = 'col-md-8';
    const MD9 = 'col-md-9';
    const MD10 = 'col-md-10';
    const MD11 = 'col-md-11';
    const MD12 = 'col-md-12';
    const LG1 = 'col-lg-1';
    const LG2 = 'col-lg-2';
    const LG3 = 'col-lg-3';
    const LG4 = 'col-lg-4';
    const LG5 = 'col-lg-5';
    const LG6 = 'col-lg-6';
    const LG7 = 'col-lg-7';
    const LG8 = 'col-lg-8';
    const LG9 = 'col-lg-9';
    const LG10 = 'col-lg-10';
    const LG11 = 'col-lg-11';
    const LG12 = 'col-lg-12';
    #endregion

    protected $childs;
    protected $custom_class;
    protected $default_class = 'col-md-12';

    protected $useLabelField = false;

    /**
     * GridColumn constructor.
     * @param object $child
     * @param string $class
     * @param string $colStyle
     */
    public function __construct($child, $class = null, $colStyle = null)
    {
        parent::__construct('div');

        $this->setClass($class);

        $this->style .= $colStyle;
        $this->addChild($child);
    }

    public function show()
    {
        $this->class = $this->getFormatedClasses();// $this->getClass() ?? $this->default_class;

        if ($this->useLabelField) {
            $child = $this->getChilds(0);
            if (!is_a($child, Button::class)) {
                $child->useLabelField();

                parent::add($child);
                parent::show();

                return $this->childs;
            }

            parent::add($this->childs[0]);
            parent::show();

            return $this->childs;
        }

        foreach ($this->childs as $child) {
            parent::add($child);
        }

        parent::show();

        return $this->childs;
    }

    public function addChild($child)
    {
        $this->childs[] = $this->getElement($child);
    }

    public function getElement($element)
    {
        if (is_string($element)) {
            $element = new TLabel($element);
            $element->class = 'control-label';
        }

        return $element;
    }

    /**@return FormField*/
    public function getChilds(int $position = null)
    {
        if (is_null($position)) {
            return $this->childs;
        }

        return $this->childs[$position];
    }

    public function setClass($param)
    {
        if (is_array($param)) {
            foreach ($param as $class) {
                $this->custom_class .= count($param) > 1 ? ' ' . $class : $class;
            }
            return;
        }
        $this->custom_class = $param;
    }

    public function getClass()
    {
        return $this->custom_class;
    }

    private function getFormatedClasses()
    {
        if (empty($this->custom_class)) {
            $this->custom_class = $this->default_class;
        }
        $type_classes = (is_string($this->custom_class)) ? explode(' ', $this->custom_class) : null;

        $classes = array();
        if (is_array($type_classes)) {
            foreach ($type_classes as $cls) {
                $classes[] = $cls;
            }
        }

        $this->custom_class = implode(' ', $classes);
        return $this->custom_class . ' dvi_grid_col';
    }

    public static function pack(array $elements, array $class = null, array $style = null)
    {
        $box = new VBox();
        $box->{'style'} = 'display:block; ';

        if ($elements) {
            foreach ($elements as $element) {
                $box->add($element);
            }
        }
        $column = new GridColumn($box, $class, $style);
        return $column;
    }

    public function useLabelField(bool $bool)
    {
        $this->useLabelField = $bool;
    }

    public function getElementLabel()
    {
        $element = $this->childs[0];
        if (is_subclass_of($element, TField::class)) {
            /**@var TField $element */
            return $element->getLabel();
        }
    }
}
