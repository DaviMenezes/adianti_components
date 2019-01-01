<?php

namespace Dvi\Adianti\Model;

use Adianti\Base\Lib\Database\TExpression;
use Adianti\Base\Lib\Database\TRecord;
use Adianti\Base\Lib\Database\TRepository;
use Adianti\Base\Lib\Database\TTransaction;
use Dvi\Adianti\Database\Transaction;
use Dvi\Adianti\Helpers\Reflection;
use Exception;
use ReflectionObject;
use ReflectionProperty;

/**
 * Classe Auxiliadora na criação de querys manuais
 *
 * É possível criar queries complexas e aplicar filtros. A consulta e criada usando PDO.
 * @package    model
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class ActiveRecord extends TRecord
{
    const TABLENAME = '';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    protected $current_obj;

    #region [BUILD MODEL] *******************************************
    public function __set($property, $value)
    {
        $this->$property = $value;
        parent::__set($property, $value);
    }

    public function store()
    {
        $properties = $this->getPublicProperties();
        foreach ($properties as $property => $value) {
            if (!empty($value)) {
                $this->data[$property] = $value;
            }
        }
        return parent::store();
    }

    protected function getEntity()
    {
        $class = get_class($this);
        $tablename = constant("{$class}::TABLENAME");

        if (empty($tablename)) {
            return (new \ReflectionClass($this))->getShortName();
        }
        return $tablename;
    }

    public static function getInstance($id)
    {
        $class = get_called_class();
        $obj = $class::getObject($id, $class);
        return $obj;
    }

    protected function addPublicAttributes()
    {
        $publics = $this->getPublicProperties();
        foreach ($publics as $key => $value) {
            if ($key != 'id') {
                parent::addAttribute($key);
            }
        }
    }

    public function getPublicProperties()
    {
        $properties = array();

        $reflectionProperties = (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($reflectionProperties as $property) {
            $prop = $property->name;
            $properties[$property->name] = $this->$prop;
        }
        return $properties;
    }

    public function fromArray($data)
    {
        foreach ($data as $key => $item) {
            $this->$key = $item;
        }
        parent::fromArray($data);
    }

    #endregion

    public static function remove($id = null): bool
    {
        $class = get_called_class();

        /**@var ActiveRecord $class */
        $class::where('id', '=', $id)->delete();

        return true;
    }

    //just to use return type
    public static function where($variable, $operator, $value, $logicOperator = TExpression::AND_OPERATOR): TRepository
    {
        return parent::where($variable, $operator, $value, $logicOperator);
    }

    #region [GET OBJECT] ********************************************

    /**@throws */
    public static function getObject($id, $class)
    {
        try {
            $conn = TTransaction::get();
            if ($conn) {
                return self::getObjectWithoutConnection($id, $class);
            } else {
                return self::getObjectOpeningConnection($id, $class);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**@throws */
    private static function getObjectOpeningConnection($id, $class)
    {
        try {
            Transaction::open();

            $obj = self::getObject($id, $class);

            Transaction::close();
            return $obj;
        } catch (Exception $e) {
            Transaction::rollback();
            throw new Exception($e->getMessage());
        }
    }

    private static function getObjectWithoutConnection($id, $class)
    {
        $obj = parent::find($id);
        if (!$obj) {
            $obj = new $class();
        }
        return $obj;
    }

    #endregion

    public static function getTableName()
    {
        $model = preg_replace('/([^A-Z])([A-Z])/', "$1_$2", Reflection::shortName(get_called_class()));

        $model = !empty(get_called_class()::TABLENAME) ? get_called_class()::TABLENAME : strtolower($model);
        return $model;
    }

    public function load($id)
    {
        $result = parent::load($id);
        if (is_array($result)) {
            return collection($result);
        }
        return $result;
    }

    public static function all()
    {
        $result = parent::all();
        if (is_array($result)) {
            return collection($result);
        }
        return $result;
    }
}
