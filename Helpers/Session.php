<?php
/**
 * Session Helper
 *
 * @author Dragos Ionita
 * @version 1.0
 * @date 3/06/15
 */

namespace Core\Helpers;
use \Core\Config as Cfg;

class Session {

    /**
     * Determine if session has started
     * @var boolean
     */
    private static $_sessionStarted = false;
    private static $_sessionPrefix;


    /**
     * Set session prefix
     */
    private static function setPrefix() {
        $cfg = new Cfg();
        self::$_sessionPrefix = $cfg->getConfig()['session_prefix'];
    }


    /**
     * Initialization session
     * If session has not started, start sessions
     */
    public static function init(){

        if(self::$_sessionStarted == false){
            session_start();
            self::$_sessionStarted = true;
        }

    }

    /**
     * Add value to a session
     * @param string $key   name the data to save
     * @param string $value the data to save
     */
    public static function set($key,$value = false){

        /**
         * Check whether session is set in array or not
         * If array then set all session key-values in foreach loop
         */

        self::setPrefix();

        if(is_array($key) && $value === false){

            foreach ($key as $name => $value) {
                $_SESSION[self::$_sessionPrefix.$name] = $value;
            }

        } else {
            $_SESSION[self::$_sessionPrefix.$key] = $value;
        }

    }

    /**
     * Extract item from session then delete from the session, finally return the item
     * @param  string $key item to extract
     * @return string      return item
     */
    public static function pull($key){

        self::setPrefix();
        $value = $_SESSION[self::$_sessionPrefix.$key];
        unset($_SESSION[self::$_sessionPrefix.$key]);

        return $value;
    }


    /**
     * Get item from session
     *
     * @param  string  $key       item to look for in session
     * @param  boolean $secondkey if used then use as a second key
     * @return string             returns the key
     */
    public static function get($key,$secondkey = false){

        self::setPrefix();
        if($secondkey == true){

            if(isset($_SESSION[self::$_sessionPrefix.$key][$secondkey])){
                return $_SESSION[self::$_sessionPrefix.$key][$secondkey];
            }

        } else {

            if(isset($_SESSION[self::$_sessionPrefix.$key])){
                return $_SESSION[self::$_sessionPrefix.$key];
            }

        }

        return false;
    }


    /**
     * @return string with the session id.
     */
    public static function id() {
        return session_id();
    }


    /**
     * Return the session array
     * @return array of session indexes
     */
    public static function display(){
        return $_SESSION;
    }


    /**
     * Empties and destroys the session
     */
    public static function destroy($key='') {

        if(self::$_sessionStarted == true) {

            if(empty($key)) {
                session_unset();
                session_destroy();
            } else {
                self::setPrefix();
                unset($_SESSION[self::$_sessionPrefix.$key]);
            }

        }
    }

}
