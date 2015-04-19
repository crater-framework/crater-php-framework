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
    protected static $instances = array();


    /**
     * Static method get
     */
    public static function get()
    {
        $config = new \Core\Config();
        $cfg = $config->getConfig();

        // Config information
        $type = $cfg['database']['type'];
        $host = $cfg['database']['host'];
        $name = $cfg['database']['name'];
        $user = $cfg['database']['user'];
        $pass = $cfg['database']['password'];

        // ID for database based on the group information
        $id = "$type.$host.$name.$user.$pass";

        // Checking if the same
        if (isset(self::$instances[$id])) {
            return self::$instances[$id];
        }

        try {
            $instance = new PDO("$type:host=$host;dbname=$name;charset=utf8", $user, $pass);
            $instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Setting Database into $instances to avoid duplication
            self::$instances[$id] = $instance;

            return $instance;

        } catch (PDOException $e) {
            // In the event of an error record the error to errorlog.html
            \Core\Logger::newMessage($e);
            \Core\Logger::customErrorMsg();
        }

        return false;
    }
}