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

    protected $foreign_keys = array();
    protected $current_obj;

    #region [BUILD MODEL] *******************************************
    public function __get($property)
    {
        $new_property = $property;
        $pos = strpos($property, '.');
        if ($pos !== false) {
            $new_property = substr($property, 0, $pos);
        }
        if (array_key_exists($new_property, $this->foreign_keys)) {
            return $this->getAssociatedObject($property);
        }

        $result = parent::__get($property);
        return $result;
    }

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

    public function getForeignKeys()
    {
        return $this->foreign_keys;
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
            if (!array_key_exists($key, $this->foreign_keys)) {
                if ($key != 'id') {
                    parent::addAttribute($key);
                }
            }
        }
    }

    protected function getAssociatedObject($attribute)
    {
        if (empty($this->foreign_keys[$attribute])) {
            $msg_user = 'Ops... um erro ocorreu ao executar esta ação. Informe ao administrador';
            $msg_dev = 'Para chamar o método getMagicObject é necessário que o ';
            $msg_dev .= 'Objeto Associado esteja mapeado. Use o método setMap no construtor do Modelo';
            throw new \Exception($msg_user, $msg_dev);
        }

        $attribute_class = $this->foreign_keys[$attribute]['class'];
        $attribute_id = $attribute . '_id';

        $this->current_obj = $this->$attribute ?? $this->current_obj;
        if (empty($this->current_obj)) {
            $this->current_obj = new $attribute_class($this->$attribute_id);
            $this->$attribute = $this->current_obj;
        }
        return $this->$attribute ?? $this->current_obj;
    }

    public function getPublicProperties()
    {
        $properties = array();

        $vars = (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($vars as $var) {
            $prop = $var->name;
            $properties[$var->name] = $this->$prop;
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
}
