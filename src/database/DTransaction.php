<?php
namespace Dvi\Adianti\Database;

use Adianti\Database\TTransaction;
use Adianti\Log\AdiantiLoggerInterface;
use Closure;

/**
 * Model DTransaction
 *
 * @version    Dvi 1.0
 * @package    database
 * @subpackage dvi
 * @author     Davi Menezes
 * @copyright  Copyright (c) 2017. (davimenezes.dev@gmail.com)
 * @link https://github.com/DaviMenezes/Dvi-PHP-Framework-for-Adianti
 */
class DTransaction
{
    private $conn;
    private static $already_connection;

    public static function open($database = 'default', array $dbinfo = NULL)
    {
        self::$already_connection = TTransaction::get();
        if (!self::$already_connection) {
            TTransaction::open($database, $dbinfo);
        }
    }

    public static function get()
    {
        return TTransaction::get();
    }

    public static function rollback()
    {
        TTransaction::rollback();
    }

    public static function close(bool $force = false)
    {
        if (!self::$already_connection or $force) {
            TTransaction::close();
        }
    }

    public static function setLoggerFunction(Closure $logger)
    {
        TTransaction::setLoggerFunction($logger);
    }

    public static function setLogger(AdiantiLoggerInterface $logger)
    {
        TTransaction::setLogger($logger);
    }

    public static function log($message)
    {
        TTransaction::log($message);
    }

    public static function getDatabase()
    {
        return TTransaction::getDatabase();
    }

    public static function getDatabaseInfo()
    {
        return TTransaction::getDatabaseInfo();
    }
}