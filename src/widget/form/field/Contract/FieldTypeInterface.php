<?php

namespace Dvi\Adianti\Widget\Form\Field\Contract;

/**
 * Field FieldTypeInterface
 *
 * @package    Field
 * @subpackage Form
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
interface FieldTypeInterface
{
    public function sanitize($value);
}
