<?php
namespace Dvi\Adianti\Model;

use Adianti\Base\Lib\Database\TTransaction;
use Exception;
use PDO;
use PDOStatement;

/**
 * Model DviQueryBuilder
 *
 * @version    Dvi 1.0
 * @package    querybuilder
 * @subpackage model
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
trait DviQueryBuilder
{
    private $filters = array();
    private $params;

    /**@var PDOStatement $pdo*/
    private $pdo;
    private $preparedFilters;
    protected $sql;
    private $functions = array();

    private $already_prepared_sql;
    private $already_pdo_prepared;
    private $already_bind_params;

    public function where($field, $operator, $value = null, $value2 = null, $query_operator = 'AND')
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

    public function having($having)
    {
        $this->params['having'] = $having;
    }

    public function setFunction($function)
    {
        $this->functions[] = $function;
    }

    public function get($position = null)
    {
        if ($position != null) {
            $this->params['limit'] = 1;
        }
        $this->prepareSql();

        $this->pdoPrepare();

        $this->bindParam();

        $this->pdo->execute();
        $objects = $this->pdo->fetchAll(PDO::FETCH_OBJ);

        $results = false;
        if (!empty($objects[0])) {
            $results = $objects;
        }
        if (isset($position)) {
            return $results[$position] ?? null;
        } else {
            return $results ?? null;
        }
    }

    public function count()
    {
        return $this->getRowCount();
    }

    #region [PDO] ***************************************************
    public function getRowCount()
    {
        $this->prepareSql();

        $this->pdoPrepare();

        $this->bindParam();

        $this->pdo->execute();
        $count = $this->pdo->rowCount();

        return $count;
    }

    private function prepareSql()
    {
        if ($this->already_prepared_sql) {
            return true;
        }

        $this->prepareSqlFilters();
        $this->prepareSqlParams();

        $this->already_prepared_sql = true;
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
                    $this->sql .= ($key + 1 < $qtd_filters) ? ' ' . $filter->query_operator .' ' : '';
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
            $this->sql .= isset($this->params['having']) ? (' having '. $this->params['having']) : '';
        }
        return $this;
    }

    private function pdoPrepare()
    {
        if ($this->already_pdo_prepared) {
            return true;
        }

        try {
            $conn = TTransaction::get();
            if ($conn == null){
                throw new Exception('Abra uma conexão antes de executar uma consulta ao banco');
            }
            $this->pdo = $conn->prepare($this->sql);

            $this->already_pdo_prepared = true;
        } catch (Exception $e) {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new Exception($e->getMessage());
        }
    }

    private function bindParam()
    {
        if ($this->already_bind_params) {
            return true;
        }
        foreach ($this->filters as $key => $filter) :

            if (isset($filter->filter) and isset($filter->value)) {
                $operator = strtolower($filter->operator);
                $value = strtolower($filter->value);
                if ($operator == 'between') {
                    $this->pdo->bindParam($filter->filter, $filter->value);
                    $this->pdo->bindParam($filter->filter.'2', $filter->value2);//Todo check if exists filter2 in construction filters
                } elseif ($operator == 'is not' or $operator == 'is') {
                    $this->pdo->bindValue($filter->filter, $value == 'null' ? null : $filter->value);
                } else {
                    $this->pdo->bindParam($filter->filter, $filter->value);
                }
            }
        endforeach;

        $this->already_bind_params = true;
    }
    #endregion
}