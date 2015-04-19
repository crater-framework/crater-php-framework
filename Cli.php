<?php
/**
 * CLI (Command Line Interface) Library
 *
 * @author Dragos Ionita
 * @version 1.0
 * @date 3/20/15
 */

namespace Core;
use Core\Cli\Screen,
    Core\Cli\Migrate,
    Core\Cli\Build;

class Cli {

    /**
     * @var array $arguments Command line arguments
     */
    public $arguments;

    public function __construct($arguments) {
        require_once __DIR__. '/../../autoload.php';

        // Remove file name
        array_shift($arguments);

        $this->arguments = $arguments;

        if (count($this->arguments) == 0) Screen::main();

        if ($arguments[0] == 'migrate') {
            $this->migrate();
        } elseif ($arguments[0] == 'build') {
            $this->build();
        } elseif ($arguments[0] == 'version') {
            $this->version();
        } else {
            Screen::main();
        }
    }


    /**
     * Call Migrate class
     */
    public function migrate() {
        new Migrate($this->arguments);
    }


    /**
     * Call Build class
     */
    public function build() {
        new Build($this->arguments);
    }


    /**
     * Print framework version
     */
    public function version() {
        echo 'Version: '.\Core\Crater::getVersion()."\r\n";
    }
}

new Cli($argv);