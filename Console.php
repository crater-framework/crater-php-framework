<?php
/**
 * Console Router
 *
 * @author Dragos Ionita
 * @version 1.0
 * @date 3/24/15
 */

namespace Core;
use Core\Cli\Utils;

class Console {

	public static $route = null;
	public static $parameters = null;

	/**
	 * Add route
	 * @param string $route Route
	 * @param array $parameters Command parameters
	 */
	public static function add($route, $parameters) {
		self::$route = $route;
		array_shift($parameters);
		self::$parameters = $parameters;
	}


	/**
	 * Console route dispatcher
	 * @throws Cli\Exception
	 */
	public static function dispatch() {

		if (is_null(self::$route)) die(Utils::colorize("No console route found", 'FAILURE'));

		// Grab the controller name and method call
		$segments = explode('@',self::$route);

		$controller = $segments[0];
		// Compiled action name
		$action = $segments[1].'Action';

		$object = new $controller();
		if (!method_exists($object, $action)) die(Utils::colorize("No action '{$segments[1]}' find in {$controller}!", 'FAILURE'));

		call_user_func_array(array($controller, $action),self::$parameters);
	}
}