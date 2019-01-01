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
 * @see https://t.me/davimenezes
 */
class RelationshipModelType
{
    use Reflection;

    protected $class_name;
    public $type;

    public function __construct(string $class_name, $type)
    {
        if (!is_subclass_of($class_name, DviModel::class)) {
            throw new \Exception('A classe modelo precisa ser do tipo ' . DviModel::class);
        }
        $this->class_name = $class_name;
        $this->type = $type;
    }

    public function getClassName()
    {
        return $this->class_name;
    }
}
