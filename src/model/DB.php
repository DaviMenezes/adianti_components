<?php

namespace Dvi\Adianti\Model;

use Closure;
use Dvi\Adianti\Database\Transaction;

/**
 * DviDefaultQuery
 * Create easy query
 * @package    Model
 * @subpackage Components
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DB
{
    use QueryBuilder;

    public static function transaction(Closure $closure)
    {
        try {
            Transaction::open();
            $result = call_user_func($closure);
            Transaction::close();
            return $result;
        } catch (\Exception $e) {
            Transaction::rollback();
            throw new \Exception('Transação-'.$e->getMessage());
        }
    }

    public function setDefaultQuery($sql)
    {
        $this->sql = $sql;
        return $this;
    }

    public static function model(string $model_class)
    {
        $query = new self();
        $query->table($model_class);

        return $query;
    }
}
