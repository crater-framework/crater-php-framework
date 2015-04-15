<?php
/**
 * Configuration Manager
 *
 * @author Dragos Ionita
 * @version 1.1
 * @date 3/10/15
 */

namespace Core;

class Config {

    public $configData;

    public function __construct(){
        $this->fileParser();
    }

    public function globalInit() {
        // Turn on output buffering
        ob_start();

        $cfg = $this->configData;

        // Turn on custom error handling
        if (isset($cfg['crater_error_handler']) && $cfg['crater_error_handler'] == true) {
            set_exception_handler('Core\Logger::exception_handler');
            set_error_handler('Core\Logger::error_handler');
        }

        // Set timezone
        if (isset($cfg['default_timezone'])) {
            date_default_timezone_set($cfg['default_timezone']);
        }

        // Start sessions
        \Core\Helpers\Session::init();

        // Set the default template
        if (isset($cfg['default_template'])){
            \Core\Helpers\Session::set('template', $cfg['default_template']);
        } else {
            die('Please set variable "default_template" in your config.');
        }
    }

    /*
     * Get app configurations
     * @return array
     */
    public function getConfig() {
       return $this->configData;
    }


    /*
     * Configuration file parser
     */
    public function fileParser() {

        $globalFile = dirname(__DIR__) .'/App/Config/global.php';
        $localFile = dirname(__DIR__) .'/App/Config/local.php';

        $data = $global = include($globalFile);

        if (file_exists($localFile)) {

            $local = include($localFile);

            // Overlay data config
            $data = array_replace($global, $local);
        }

        // Config validators
        if (!isset($data['session_prefix'])) $data['session_prefix'] = '';
        if (!isset($data['language_code'])) $data['language_code'] = 'en';

        $this->configData = $data;
    }
}
