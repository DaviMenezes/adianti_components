<?php

namespace Dvi\Adianti\Widget\Form\Field\Validator;

use Dvi\Adianti\Database\Transaction;
use Dvi\Adianti\Helpers\Reflection;
use Dvi\Adianti\Model\DB;
use Dvi\Adianti\Model\DviModel;

/**
 * Validator UniqueValidator
 *
 * @package    Validator
 * @subpackage Field
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
  * @see https://t.me/davimenezes
 */
class UniqueValidator extends FieldValidator
{
    protected $property;
    protected $default_msg;
    protected $service_provider;

    public function __construct($service, $property)
    {
        parent::__construct($parameters['msg'] ?? null);

        $this->service_provider = $service;
        $this->property = $property;
    }

    public function validate($label, $value, $parameters = null)
    {
        if (method_exists($this->service_provider, 'validate')) {
            $parameters['property'] = $this->property;

            if ((new $this->service_provider())->validate($parameters)) {
                return true;
            }
            $this->error_msg = $this->error_msg ?? 'Campo único: '.$value.' já existe.';
            return false;
        }


        $request = $parameters['request'] ?? array();
        $diferent_form_property_name = Reflection::lowerName($this->model).'_id';

        $different_form_property_value = null;
        foreach ($request as $item => $value) {
            $property = str_replace('-', '_', $item);

            if ($property == $diferent_form_property_name) {
                $different_form_property_value = $value;
            }
        }

        if (!$different_form_property_value) {
            return true;
        }

        Transaction::open();
        $count = $this->model::where('id', '<>', $different_form_property_value)->where($this->property, '=', $value)->count();
        Transaction::close();

        if ($count > 0) {
            $this->error_msg = $this->error_msg ?? 'Campo único: '.$value.' já existe.';
            return false;
        }
        return true;
    }
}
