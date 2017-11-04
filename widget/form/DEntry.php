<?php
namespace Dvi\Widget\Form;

use Adianti\Validator\TRequiredValidator;
use Adianti\Widget\Form\TEntry;
use Dvi\Widget\Form\Field\FieldEntry;
use Dvi\Widget\Form\Field\SearchableField;
use Dvi\Widget\IDviWidget;

/**
 * Model DEntry
 *
 * @version    Dvi 1.0
 * @package    form
 * @subpackage widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DEntry extends FieldEntry
{
    use SearchableField;

    public function __construct(string $name, string $placeholder = null, bool $required = false, bool $tip = true)
    {
        parent::__construct($name, $placeholder, $required, $tip);

        $this->operator('like');
    }

    public static function create(string $name, string $placeholder = null, bool $required = false, bool $tip = true)
    {
        $obj = new DEntry($name, $placeholder, $required, $tip);

        return $obj;
    }

}
