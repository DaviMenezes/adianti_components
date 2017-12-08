<?php

namespace Dvi\Adianti\Model;

/**
 * Model DviModel
 *
 * @version    Dvi 1.0
 * @package    Model
 * @subpackage Components
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
abstract class DviModel extends DviTRecord
{
    use ModelForm;

    abstract public function buildStructureForm();
}
