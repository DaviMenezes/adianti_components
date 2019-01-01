<?php

namespace Dvi\Adianti\Model;

use Adianti\Base\Lib\Registry\TSession;
use Dvi\Adianti\Database\Transaction;
use Dvi\Adianti\Helpers\Reflection;

/**
 *  DBFormFieldPrepare
 * Prepare field data to database query
 * @package    Model
 * @subpackage DviAdianti Components
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DBFormFieldPrepare extends DB
{
    protected $model;
    protected $called_class;

    use Reflection;

    public function __construct($model, $called_class = null)
    {
        $this->model = $model;
        $this->called_class = $called_class;
    }

    public function mountQueryByFields(array $fields)
    {
        $columns = array();
        $this->prepareFields($fields, $columns);

        $this->table($this->model, Reflection::shortName($this->model));

        foreach ($this->getJoins($fields) as $alias => $item) {
            $this->addStringJoin($item);
        }
    }

    public function prepareFields(array $fields, &$columns)
    {
        foreach ($fields as $column_name_alias) {
            $pos = strpos($column_name_alias, '.');
            if ($pos !== false) {
                $column_name_array = explode('.', $column_name_alias);
                foreach ($column_name_array as $key => $item) {
                    if ($key + 1 == count($column_name_array)) {
                        $name_key = $key > 0 ? $key - 1 : 0;
                        $column_name2 = ucfirst($column_name_array[$name_key]) . '.' . $item;
                        $columns[] = $column_name2 . ' as "' . $column_name_alias . '"';
                    }
                }
            } else {
                $columns[] = self::shortName($this->model) . '.' . $column_name_alias;
            }
        }
        parent::fields($columns);
    }

    public function getJoins($fields): array
    {
        $joins = array();
        foreach ($fields as $field_key => $column_name_alias) {
            if (strpos($column_name_alias, '.') === false) {
                continue;
            }
            /**@var DviModel $last_association */
            $last_association = new $this->model();
            $column_name_array = explode('.', $column_name_alias);
            foreach ($column_name_array as $key => $item) {
                if (collect($joins)->has($item) or !$last_association->relationships()->has($item)) {
                    continue;
                }
                $relationshipModelType = $last_association->getRelationship($item);
                $association_class = $relationshipModelType->getClassName();
                $joins[$item] = $last_association->getJoin($association_class);
                $last_association = new $association_class();
            }
        }
        return $joins;
    }

    public function checkFilters($class)
    {
        try {
            $called_class = Reflection::shortName($class);

            $filters = TSession::getValue($called_class . '_filters');
            if ($filters) {
                $this->filters($filters);
            }

            $order = TSession::getValue($called_class . '_listOrder');
            if ($order) {
                $this->order($order['field'], $order['direction']);
            }
        } catch (\Exception $e) {
            Transaction::rollback();
            throw new \Exception('Verificando os filtros-' . $e->getMessage());
        }
    }
}
