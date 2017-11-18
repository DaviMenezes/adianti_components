<?php

namespace Dvi\Adianti\Model;

use Adianti\Database\TExpression;
use Adianti\Database\TRecord;
use Adianti\Database\TRepository;
use Adianti\Database\TTransaction;
use Exception;
use Dvi\Adianti\Database\DTransaction;
use PDO;
use ReflectionObject;
use ReflectionProperty;

/**
 * Classe Auxiliadora na criação de querys manuais
 *
 * É possível criar queries complexas e aplicar filtros. A consulta e criada usando PDO.
 *
 * @version    Dvi 1.0
 * @package    model
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DviTRecord extends TRecord
{
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    private $filters = array();
    protected $sql;
    private $params;
    private $pdo;
    private $functions = array();
    private $preparedFilters;

    private $privates = array();
    private $objects = array();
    private $field_types = array();

    public function __construct($id = null, $callObjectLoad = true)
    {
        parent::__construct($id, $callObjectLoad);

        $this->addPublicAtributes();
    }

    #region [BUILD MODEL] *******************************************
    public function __get($property)
    {
        if(array_key_exists($property, $this->privates))
        {
            return $this->getMagicObject($property);
        }

        return parent::__get($property);

    }

    public function setMap(array $atributes) {
        foreach ($atributes as $key => $class) {
            $this->privates[$key] = $class;
            parent::addAttribute($key.'_id');
        }
    }

    private function addPublicAtributes()
    {
        $publics = $this->getPublicProperties();
        foreach($publics as $key => $public) {
            if (!array_key_exists($key, $this->privates)) {
                parent::addAttribute($key);
            }

        }
    }

    private function getMagicObject($atribute)
    {
        $obj = $this->objects[$atribute] ?? null;
        if(empty($obj)) {
            $atribute_id = $atribute.'_id';
            $atribute_class = $this->privates[$atribute];
            $this->objects[$atribute] = new $atribute_class($this->$atribute_id);
        }
        return $this->objects[$atribute];
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
    #endregion


    public static function remove($id = null) : bool
    {
        /**@var DviTRecord $class*/
        $class = get_called_class();
        $class::where('id', '=', $id)->delete();
        
        return true;
    }

    //just to use return type
    public static function where($variable, $operator, $value, $logicOperator = TExpression::AND_OPERATOR): TRepository
    {
        return parent::where($variable, $operator, $value, $logicOperator);
    }

    public function setFilter(DviTFilter $filter)
    {
        $this->filters[] = $filter;
        return $this;
    }

    public function addwhere($field, $operator, $value = null, $value2 = null, $query_operator = 'AND')
    {
        $dvifilter = new DviTFilter($field, $operator, null, $value, $value2, $query_operator);
        $this->filters[] = $dvifilter;
        return $this;
    }

    public function order(string $order)
    {
        $this->params['order'] = $order;
        return $this;
    }

    public function groupby($groupby)
    {
        $this->params['group by'] = $groupby;
        return $this;
    }

    // deprecated
    public function setQueryProperties($params)
    {
        $this->params = $params;
    }

    public function getResult($position = null)
    {
        $this->prepare();

        $this->pdoPrepare();
//        $sql = $this->createQuery($query, $params);


        $this->bindParam();

        $this->pdo->execute();
        $objects = $this->pdo->fetchAll(PDO::FETCH_OBJ);
        
        $results = false;
        if (!empty($objects[0])) {
            $results = $objects;
        }
        if (isset($position)) {
            return $results[$position];
        } else {
            return $results;
        }
    }

    protected function setFieldTypes($types)
    {
        $this->field_types = $types;
    }

    public function getFieldTypes()
    {
        return $this->field_types;
    }

    private function createQuery()
    {
        $this->prepareSqlFilters();

        $this->prepareSqlParams();

        return $this;
    }

    protected function setSql($sql)
    {
        $this->sql = $sql;
        return $this;
    }

    public function getRowCount()
    {
        $this->prepareSqlFilters();
        $this->pdoPrepare();
        $this->bindParam();
        $this->pdo->execute();
        $count = $this->pdo->rowCount();

        return $count;
    }

    private function prepareSqlFilters()
    {
        if ($this->preparedFilters) {
            return $this;
        }
        if (!empty($this->filters)) {
            $this->sql .= ' WHERE ';

            foreach ($this->filters as $key => $filter) {
                $qtd_filters = count($this->filters);

                $filter->filter = ':'. str_replace('.', '_', $filter->field) . $key;

                if ($filter->operator == 'not in') {
                    $this->sql .= $filter->field . ' '.$filter->operator. ' ('.$filter->filter.')';
                } else {
                    $this->sql .= ' ' . $filter->field . ' ' . $filter->operator . ' ' . $filter->filter;
                }

                if ($filter->operator == 'BETWEEN') {
                    $this->sql .= ' AND ' . $filter->filter . '2';
                }

                if ($qtd_filters > 1) {
                    $this->sql .= ($key + 1 < $qtd_filters) ? ' ' . $filter->query_operator : '';
                }
            }
        }

        if (!empty($this->functions)) {
            $new_where = null;
            if (strpos($this->sql, 'WHERE') === false) {
                $new_where = true;
                $this->sql .= ' WHERE ';
            }
            foreach ($this->functions as $function) {
                $this->sql .=  $new_where ? $function : ' and '. $function;
            }
        }
        $this->preparedFilters = true;
        return $this;
    }

    private function prepareSqlParams()
    {
        if ($this->params) {
            $this->sql .= isset($this->params['group by']) ? (' group by ' . $this->params['group by']) : '';
            $this->sql .= isset($this->params['order']) ? (' order by ' . $this->params['order'] . ' ' . $this->params['direction']) : '';
            $this->sql .= isset($this->params['limit']) ? (' limit ' . $this->params['limit']) : '';
        }
        return $this;
    }

    private function bindParam()
    {
        foreach ($this->filters as $key => $filter) :

            if (isset($filter->filter) and isset($filter->value)) {
                if ($filter->operator == 'BETWEEN') {
                    $this->pdo->bindParam($filter->filter, $filter->value);
                    $this->pdo->bindParam($filter->filter.'2', $filter->value2);//Todo check if exists filter2 in construction filters
                } elseif ($filter->operator == 'is not') {
                    $this->pdo->bindValue($filter->filter, $filter->value == 'null' ? null : $filter->value);
                } else {
                    $this->pdo->bindParam($filter->filter, $filter->value);
                }
            }
        endforeach;
    }

    public function setFunction($function)
    {
        $this->functions[] = $function;
    }

    private function prepare()
    {
        $this->prepareSqlFilters();
        $this->prepareSqlParams();
    }

    private function pdoPrepare()
    {
        $conn = TTransaction::get();
        $this->pdo = $conn->prepare($this->sql);
    }

    #region [GET OBJECT] ********************************************
    /**@throws */
    protected static function getObject($id, $class)
    {
        try{
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

    /**@throws*/
    private static function getObjectOpeningConnection($id, $class)
    {
        try {
            DTransaction::open();

            $obj = self::getObject($id, $class);

            DTransaction::close();
            return $obj;
        } catch (Exception $e) {
            DTransaction::rollback();
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
}
