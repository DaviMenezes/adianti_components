<?php

namespace Dvi\Adianti\Model;

use Adianti\Base\Lib\Registry\TSession;
use Dvi\Adianti\Database\Transaction;
use Dvi\Adianti\Helpers\Reflection;

/**
 * Model DBFormFieldPrepare
 *
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

        $this->table($this->model, Reflection::getClassName($this->model));

        foreach ($this->getJoins($fields) as $alias => $item) {
            $this->join($item['model_class'], $item['foreign_key'], $item['table_alias'], 'left', $item['associated_alias']);
        }
    }

    public function prepareFields(array $fields, &$columns)
    {
        foreach ($fields as $column_name_alias) {
            $pos = strpos($column_name_alias, '.');
            if ($pos !== false) {
                $column_name_array = explode('.', $column_name_alias);
                foreach ($column_name_array as $key => $item) {
                    if ($key+1 == count($column_name_array)) {
                        $name_key = $key > 0 ? $key -1 : 0;
                        $column_name2 = ucfirst($column_name_array[$name_key]) . '.' . $item;
                        $columns[] = $column_name2 .' as "' . $column_name_alias . '"';
                    }
                }
            } else {
                $columns[] = self::getClassName($this->model).'.'. $column_name_alias;
            }
        }
        parent::fields($columns);
    }

    public function getJoins($fields):array
    {
        $joins = array();
        foreach ($fields as $field_key => $column_name_alias) {
            $pos = strpos($column_name_alias, '.');
            if ($pos !== false) {
                /**@var DviModel $last_association*/
                $last_association = new $this->model();
                $forein_keys = $last_association->getForeignKeys();
                $column_name_array = explode('.', $column_name_alias);
                $associated_alias = Reflection::getClassName($last_association);
                foreach ($column_name_array as $key => $item) {
                    $table_alias = ucfirst($item);
                    if (!isset($forein_keys[$item])) {
                        throw new \Exception('Processo: Construção da query (joins). O Modelo '.$item.' não foi encontrado ('.$field_key.')');
                    }

                    if (array_key_exists($item, $forein_keys) and !array_key_exists($table_alias, $joins)) {
                        $join['model_class'] = $forein_keys[$item]['class'];
                        $join['table_alias'] = $table_alias;
                        $join['associated_alias'] = $associated_alias;
                        $join['table'] = $last_association::getTableName();
                        $join['foreign_key'] = $item.'_id';

                        $joins[$table_alias] = $join;
                    }
                    if ($key+1 == count($column_name_array)-1) {
                        break;
                    }
                    $associated_alias = ucfirst($forein_keys[$item]['alias']);
                    $last_association = $forein_keys[$item]['class'];
                    /**@var DviTRecord $model_class*/
                    $model_class = $forein_keys[$item]['class'];
                    $forein_keys = (new $model_class())->getForeignKeys();
                }
            }
        }
        return $joins;
    }

    public function checkFilters($class)
    {
        try {
            $called_class = Reflection::getClassName($class);

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
            throw new \Exception('Verificando os filtros-'.$e->getMessage());
        }
    }
}
