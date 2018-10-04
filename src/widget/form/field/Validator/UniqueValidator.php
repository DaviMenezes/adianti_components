<?php

namespace Dvi\Adianti\Widget\Form\Field\Validator;

use Dvi\Adianti\Database\Transaction;
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
 */
class UniqueValidator extends FieldValidator
{
    /**@var DviModel $model*/
    protected $model;
    protected $property;
    protected $default_msg;

    public function __construct($model, $property, $msg = null)
    {
        parent::__construct($msg);

        $this->model = $model;
        $this->property = $property;
        $this->default_msg = $msg;
    }

    public function validate($label, $value, $parameters = null)
    {
        Transaction::open();
        $count = $this->model::where($this->property, '=', $value)->count();
        Transaction::close();

        if ($count > 0) {
            $this->error_msg = $this->error_msg ?? 'Campo único: '.$value.' já existe.';
            return false;
        }
        return true;
    }
}
