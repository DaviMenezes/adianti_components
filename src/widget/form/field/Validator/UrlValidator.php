<?php

namespace Dvi\Adianti\Widget\Form\Field\Validator;

use Adianti\Base\Lib\Core\AdiantiCoreTranslator;
use Adianti\Base\Lib\Validator\TFieldValidator;

/**
 * Validator UrlValidator
 * @package    Validator
 * @subpackage Adianti
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class UrlValidator extends TFieldValidator
{
    public function validate($label, $value, $parameters = null)
    {
        $filter = filter_var(trim($value), FILTER_VALIDATE_URL);

        if ($filter === false) {
            throw new \Exception(AdiantiCoreTranslator::translate('The field ^1 contains an invalid url', $label));
        }
    }
}
