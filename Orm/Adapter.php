<?php
/**
 * Database Adapter
 *
 * @author Dragos Ionita
 * @version 1.1
 * @date 3/10/15
 */

namespace Core\Orm;

use \PDO;

class Adapter
{

    /**
     * @var array Array of saved databases for reusing
     */
    protected static $instance = null;
    protected static $settings=array();
    /**
     *
     *Private construct , avoid  create an instance
     */
    private function __construct(){
        $config = new \Core\Config();
        $cfg = $config->getConfig();
        $type = $cfg['database']['type'];
        $host = $cfg['database']['host'];
        $name = $cfg['database']['name'];
        $user = $cfg['database']['user'];
        $pass = $cfg['database']['password'];
        $key = "$type.$host.$name.$user.$pass";
        $connector=null;
        try {
            $connector = new PDO("$type:host=$host;dbname=$name;charset=utf8", $user, $pass);
            $connector->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // register a new database config into settings
            if(is_null(self::getConfig($key))){
                self::$settings[$key]=$connector;
            }

            return $connector;

        } catch (PDOException $e) {
            // In the event of an error record the error to errorlog.html
            \Core\Logger::newMessage($e);
            \Core\Logger::customErrorMsg();
        }

        return $connector;

    }

    /**
     * Static method getInstance
     */
    public static function getInstance()
    {
        self::$instance=new Adapter();
        return self::$instance;
    }

    public static function getConfig($key)
    {
        if (!isset(self::$settings[$key])) {
            return null;
        }
        return self::$settings[$key];
    }
}