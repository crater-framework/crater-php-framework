<?php
/**
 * Bootstrap
 *
 * @author Dragos Ionita
 * @version 1.1
 * @date 3/06/15
 */

namespace Core;

use \Core\Router,
    \Core\Console;

class Bootstrap
{

    protected $consoleArguments;

    public function __construct($arguments)
    {

        $config = new \Core\Config();
        $config->globalInit();

        if (!is_null($arguments)) {
            array_shift($arguments);
            $this->consoleArguments = $arguments;
            $this->initConsole();
        } else {
            $this->initRouter();
        }
    }


    /**
     * Initialize the console
     */
    protected function initConsole()
    {
        $consoles = $this->setConsoles();
        foreach ($consoles as $console) {

            if ($console[0] == $this->consoleArguments[0]) {
                Console::add($console[1], $this->consoleArguments);
            }
        }

        Console::dispatch();
    }


    /**
     * Initialize the router
     */
    protected function initRouter()
    {

        // Get all routes from local bootstrap
        $routes = $this->setRoutes();

        // Send all routes to Router
        foreach ($routes as $route) {
            if ($route[0] == 'any') {
                Router::any($route[1], $route[2]);
            } elseif ($route[0] == 'get') {
                Router::get($route[1], $route[2]);
            } elseif ($route[0] == 'post') {
                Router::post($route[1], $route[2]);
            } else {
                echo error::display('Route Error: bad declaration');
            }
        }

        // Set default controller and action for errors
        Router::error('\Core\Error@index');
        Router::dispatch();
    }
}