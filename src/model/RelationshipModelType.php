<?php

namespace Dvi\Adianti\Model;

use Dvi\Adianti\Helpers\Reflection;

/**
 *  RelationshipModelType
 *
 * @package
 * @subpackage
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @see https://github.com/DaviMenezes
 */
class RelationshipModelType
{
    use Reflection;

    protected $class_name;
    public $type;

    public function __construct(string $model, int $type)
    {
        if (!is_subclass_of($model, DviModel::class)) {
            throw new \Exception('A classe modelo precisa ser do tipo ' . DviModel::class);
        }
        $this->class_name = $model;
        $this->type = $type;
    }

    public function getClassName()
    {
        return $this->class_name;
    }
}
