<?php

namespace Dvi\Adianti\Widget\Form;

use Adianti\Base\Lib\Widget\Dialog\TMessage;
use Adianti\Base\Lib\Widget\Form\TRadioGroup;
use Dvi\Adianti\Widget\Form\Field\SearchableField;

/**
 * DRadioGroup
 *
 * @version    Dvi 1.0
 * @package    Form
 * @subpackage Widget
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DRadioGroup extends TRadioGroup
{
    use SearchableField;

    public function __construct(string $name, $placeholder, $required = false)
    {
        try {
            parent::__construct($name);

            $this->setLayout('horizontal');
            $this->setUseButton();

            $fc = mb_strtoupper(mb_substr($placeholder, 0, 1));
            $label = $fc.mb_substr($placeholder, 1);

            $this->setLabel($label);

            $this->operator('=');

            if ($required) {
                $this->addValidation($label);
            }

        } catch (\Exception $e) {
            new TMessage('info', $e->getMessage());
        }
    }
    public function items(array $items)
    {
        parent::addItems($items);
    }
}
