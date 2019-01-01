<?php

namespace Dvi\Adianti\Model;

use Dvi\Adianti\Database\Transaction;
use Exception;
use PDO;
use PDOStatement;
use ReflectionClass;

/**
 *  QueryBuilder
 * @package    querybuilder
 * @subpackage model
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
trait QueryBuilder
{
    protected $table;
    protected $fields = array();
    protected $joins = array();
    protected $str_joins = array();

    protected $filters = array();
    protected $params;

    /**@var PDOStatement */
    protected $pdo;
    protected $preparedFilters;
    protected $sql;
    protected $functions = array();
    protected $pdo_fetch;

    protected $already_prepared_sql;
    protected $already_pdo_prepared;
    protected $already_bind_params;
    protected $already_set_group_by;
    protected $already_set_order_by;
    protected $already_set_limit;
    protected $already_set_offset;
    protected $already_set_having;
    protected $result;

    public function where($field, $operator, $value = null, $value2 = null, $query_operator = 'AND')
    {
        $dvifilter = new QueryFilter($field, $operator, $value, $value2, $query_operator);
        $this->filters[] = $dvifilter;

        $this->preparedFilters = false;

        return $this;
    }

    public function order(string $order, $direction = 'asc')
    {
        if (strpos($order, '.') !== false) {
            $array = explode('.', $order);
            $model = ucfirst($array[0]);
            unset($array[0]);
            $order = $model . '.' . implode('.', $array);
        }
        $this->params['order'] = $order;
        $this->params['direction'] = $direction;
        return $this;
    }

    public function limit($limit)
    {
        $this->params['limit'] = $limit;
    }

    public function groupby($groupby)
    {
        $this->params['group by'] = $groupby;
        return $this;
    }

    public function having($having)
    {
        $this->params['having'] = $having;
    }

    public function setFunction($function)
    {
        $this->functions[] = $function;
    }

    public function table(string $model_class, string $alias = null)
    {
        /**@var ActiveRecord $model_class */
        $alias = $alias ?? (new ReflectionClass($model_class))->getShortName();
        $this->table = ['table' => $model_class::getTableName(), 'alias' => $alias, 'default_obj' => $model_class];
        return $this;
    }

    public function fields(array $fields)
    {
        $this->fields = $fields;
        return $this;
    }

    public function addStringJoin(string $join)
    {
        $this->str_joins[] = $join;
    }

    public function join($model_class, string $forein_key, $table_alias = null, string $type = 'inner', $associated = null)
    {
        if (!$associated) {
            $associated_alias = (new ReflectionClass($this->table['default_obj']))->getShortName();
        } else {
            $pos = strpos('\\', $associated);
            if ($pos) {
                $associated_alias = (new ReflectionClass($associated))->getShortName();
            } else {
                $associated_alias = $associated;
            }
        }

        $this->joins[] = [
            'type' => $type,
            'table' => $model_class::getTableName(),
            'table_alias' => $table_alias ?? (new ReflectionClass($model_class))->getShortName(),
            'associated_alias' => $associated_alias,
            'foreing_key' => $forein_key
        ];
        return $this;
    }

    public function offset($offset)
    {
        $this->params['offset'] = $offset;
    }

    public function getObject($class = null)
    {
        $this->prepareSql();

        $this->pdoPrepare();

        $this->bindParams();

        $this->pdo->execute();

        $this->result = $this->pdo->fetchObject();

        return $this->getResult();
    }

    public function get($limit = null, $pdo_fetch = null)
    {
        if ($limit) {
            $this->limit($limit);
        }

        $this->prepareSql();

        $this->pdoPrepare();

        $this->bindParams();

        $this->pdo->execute();

        $pdo_fetch = $pdo_fetch ?? $this->pdo_fetch;
        $result = $this->pdo->fetchAll($pdo_fetch ?? PDO::FETCH_OBJ);

        $this->setResult($result);

        return $this->getResult();
    }

    public function returnType(int $pdo_fetch)
    {
        $this->pdo_fetch = $pdo_fetch;
        return $this;
    }

    private function setResult($result)
    {
        $this->result = $result;
    }

    private function getResult()
    {
        if (is_array($this->result)) {
            return collect($this->result);
        }
        return $this->result;
    }

    public function count()
    {
        return $this->getRowCount();
    }

    public function getSql()
    {
        return $this->sql;
    }

    public function filters(array $filters)
    {
        /**@var QueryFilter $filter */
        foreach ($filters as $filter) {
            if (!is_a($filter, QueryFilter::class)) {
                throw new \Exception('Os filtros devem ser do tipo QueryFilter');
            }
            $this->where($filter->field, $filter->operator, $filter->value, $filter->value2);
        }
        return $this;
    }

    #region [PDO] ***************************************************
    public function getRowCount()
    {
        $this->prepareSql();

        $this->already_prepared_sql = false;

        $this->pdoPrepare();

        $this->already_pdo_prepared = false;

        $this->bindParams();

        $this->already_bind_params = false;

        $this->pdo->execute();
        $count = $this->pdo->rowCount();

        return $count;
    }

    private function prepareSql()
    {
        if ($this->already_prepared_sql) {
            return true;
        }
        if (empty($this->sql)) {
            $this->sql = 'SELECT ';
            $this->addFields();
            $this->addTables();
            $this->prepareJoins();
        }
        $this->prepareSqlFilters();
        $this->prepareSqlParams();

        $this->already_prepared_sql = true;
    }

    protected function addFields()
    {
        $fields = null;
        foreach ($this->fields as $key => $field_name) {
            $space = ($key + 1 < count($this->fields) ? ', ' : '');
            if (strpos($field_name, '.') === false) {
                $fields .= $this->table['alias'] . '.' . $field_name . $space;
                continue;
            }
            $fields .= $field_name . $space;
        }
        $this->sql .= $fields ?? '*';
    }

    protected function addTables()
    {
        if (!empty($this->table)) {
            $this->sql .= ' FROM ' . $this->table['table'] . ' as ' . (new ReflectionClass($this->table['default_obj']))->getShortName();
        }
    }

    protected function prepareJoins()
    {
        foreach ($this->joins as $join) {
            $this->sql .= ' ' . $join['type'] . ' join ' . $join['table'] . ' as ' . $join['table_alias'];
            $this->sql .= ' on ' . $join['table_alias'] . '.id = ' . $join['associated_alias'] . '.' . $join['foreing_key'];
        }
        foreach ($this->str_joins as $str_join) {
            $this->sql .= ' ' . $str_join;
        }
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

                $filter->filter = ':' . 'filter_' . $key;

                if ($filter->operator == 'not in') {
                    $this->sql .= $filter->field . ' ' . $filter->operator . ' (' . $filter->filter . ')';
                } else {
                    $this->sql .= ' ' . $filter->field . ' ' . $filter->operator . ' ' . $filter->filter;
                }

                if ($filter->operator == 'BETWEEN') {
                    $this->sql .= ' AND ' . $filter->filter . '2';
                }

                if ($qtd_filters > 1) {
                    $this->sql .= ($key + 1 < $qtd_filters) ? ' ' . $filter->query_operator . ' ' : '';
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
                $this->sql .= $new_where ? $function : ' and ' . $function;
            }
        }
        $this->preparedFilters = true;
        return $this;
    }

    private function prepareSqlParams()
    {
        if ($this->params) {
            $this->setGroupBy();
            $this->setOrder();
            $this->setLimit();
            $this->setOffset();
            $this->setHaving();
        }
        return $this;
    }

    private function pdoPrepare()
    {
        if ($this->already_pdo_prepared) {
            return true;
        }

        try {
            $conn = Transaction::get();
            $manual = false;
            if (!$conn) {
                Transaction::open();
                $conn = Transaction::get();
                $manual = true;
            }
            $this->pdo = $conn->prepare($this->sql);

            $this->already_pdo_prepared = true;

            if ($manual) {
                Transaction::close();
            }
        } catch (Exception $e) {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new Exception($e->getMessage());
        }
    }

    private function bindParams()
    {
        if ($this->already_bind_params) {
            return true;
        }

        foreach ($this->filters as $key => $filter) {
            if (isset($filter->filter) and isset($filter->value)) {
                $operator = strtolower($filter->operator);
                $value = strtolower($filter->value);
                if ($operator == 'between') {
                    $this->pdo->bindParam($filter->filter, $filter->value);
                    if (!empty($filter->value2)) {
                        $this->pdo->bindParam($filter->filter . '2', $filter->value2);
                    }
                } elseif ($operator == 'is not' or $operator == 'is') {
                    $value = $value == 'null' ? null : $filter->value;
                    $this->pdo->bindParam($filter->filter, $value);
                } else {
                    $this->pdo->bindParam($filter->filter, $filter->value);
                }
            }
        }

        $this->already_bind_params = true;
    }

    protected function setGroupBy()
    {
        if ($this->already_set_group_by) {
            return;
        }
        if (isset($this->params['group by'])) {
            $this->sql .= (' group by ' . $this->params['group by']);
            $this->already_set_group_by = true;
        }
    }

    protected function setOrder()
    {
        if ($this->already_set_order_by) {
            return;
        }
        if (isset($this->params['order'])) {
            $this->sql .= (' order by ' . $this->params['order'] . ' ' . $this->params['direction']);
            $this->already_set_order_by = true;
        }
    }

    protected function setLimit()
    {
        if ($this->already_set_limit) {
            return;
        }
        if (isset($this->params['limit'])) {
            $this->sql .= (' limit ' . $this->params['limit']);
            $this->already_set_limit = true;
        }
    }

    private function setHaving()
    {
        if ($this->already_set_having) {
            return;
        }
        if (isset($this->params['having'])) {
            $this->sql .= (' having ' . $this->params['having']);
            $this->already_set_having = true;
        }
    }

    protected function setOffset()
    {
        if ($this->already_set_offset) {
            return;
        }
        if (isset($this->params['offset'])) {
            $this->sql .= (' offset ' . $this->params['offset']);
            $this->already_set_offset = true;
        }
    }
    #endregion
}
