<?php

namespace Dvi\Adianti\Model;

use Closure;
use Dvi\Adianti\Database\DTransaction;
use Dvi\Adianti\Widget\Dialog\DMessage;

/**
 * Model DviDefaultQuery
 *
 * @package    Model
 * @subpackage Components
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2018. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes
 */
class DB
{
    use DviQueryBuilder;

    public static function transaction(Closure $closure)
    {
        try {
            DTransaction::open();
            $result = call_user_func($closure);
            DTransaction::close();
            return $result;
        } catch (\Exception $e) {
            DTransaction::rollback();
            DMessage::create('die', $e->getMessage());
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
