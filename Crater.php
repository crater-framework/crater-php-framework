<?php
/**
 * Main Crater Framework Class
 *
 * @author Dragos Ionita
 * @version 1.1
 * @date 3/10/15
 */

namespace Core;

class Crater {

    protected $arguments;

    public function __construct($arguments = null) {
        $this->arguments = $arguments;

        spl_autoload_register(function($lib){

            if ($lib) {
                $lib = str_replace("\\", "/", $lib);
                $namespace = explode('/',trim($lib));
                $dir = dirname(__DIR__). '/';
                if ($namespace[0] == 'Controllers' ||
                    $namespace[0] == 'Models') {
                    $dir .= 'App/';
                }
                include $dir .$lib . ".php" ;
            }

        });

        if (!is_readable(dirname(__DIR__) . '/App/Config/global.php')) {
            die('No global.php found, configure and rename global.example.php to global.php in App/Config.');
        }
    }

    static public function getVersion() {
        return "0.7.0";
    }

    public function run() {
        new \App\Bootstrap($this->arguments);
    }
}
