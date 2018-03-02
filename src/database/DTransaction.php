<?php
namespace Dvi\Adianti\Database;

use Adianti\Base\Lib\Database\TTransaction;
use Adianti\Base\Lib\Log\AdiantiLoggerInterface;
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

    public static function open($database = 'default', array $dbinfo = null)
    {
        $conn = TTransaction::get();
        if (!$conn) {
            TTransaction::open($database, $dbinfo);
        }
    }

    public static function get()
    {
        return TTransaction::get();
    }

    public static function rollback()
    {
        if (TTransaction::get()) {
            TTransaction::rollback();
        }
    }

    public static function close()
    {
        $conn = TTransaction::get();

        if ($conn) {
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