<?php
/**
 * Main Router
 * routing urls to closurs and controllers - modified from https://github.com/NoahBuscher/Macaw
 *
 * @author Dragos Ionita
 * @version 1.2
 * @date 3/4/15
 */

namespace Core;

class Router {

	// If true - do not process other routes when match is found
	public static $halts = true;

	// Set routes, methods and etc.
	public static $routes = array();
	public static $methods = array();
	public static $callbacks = array();
	public static $error_callback;

	// Set route patterns
	public static $patterns = array(
		':any' => '[^/]+',
		':num' => '[0-9]+',
		':all' => '.*'
	);


	/**
	 * Defines a route w/ callback and method
	 *
	 * @param   string $method
	 * @param   array @params
	 */
	public static function __callstatic($method, $params){

		$uri = dirname($_SERVER['PHP_SELF']).'/'.$params[0];

		$callback = $params[1];

		array_push(self::$routes, $uri);
		array_push(self::$methods, strtoupper($method));
		array_push(self::$callbacks, $callback);
	}


	/**
	 * Defines callback if route is not found
	 * @param   string $callback
	 */
	public static function error($callback){
		self::$error_callback = $callback;
	}


	/**
	 * Don't load any further routes on match
	 * @param  boolean $flag 
	 */
	public static function haltOnMatch($flag = true){
		self::$halts = $flag;
	}


	/**
	 * Call object and instantiate
	 *
	 * @param  object $callback 
	 * @param  array $matched  array of matched parameters
	 * @param  string $msg      
	 */
	public static function invokeObject($callback,$matched = null,$msg = null){

		// Grab all parts based on a / separator and collect the last index of the array
		$last = explode('/',$callback);
		$last = end($last);
		// Grab the controller name and method call
		$segments = explode('@',$last);

		// Compiled action name
		$segments[1] = $segments[1].'Action';

		// Instanitate controller with optional msg (used for error_callback)


		$controller = new $segments[0]($msg);
		$controller->setControllerName($segments[1]);

		if($matched == null){
			$controller->$segments[1]();
		} else {
			// Call method and pass in array keys as params
			call_user_func_array(array($controller, $segments[1]), $matched);
		}
	}


	/**
	 * Runs the callback for the given request
	 */
	public static function dispatch(){

		$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$method = $_SERVER['REQUEST_METHOD'];  

		$searches = array_keys(static::$patterns);
		$replaces = array_values(static::$patterns);

		self::$routes = str_replace('//','/',self::$routes);

		$found_route = false;

		// Parse query parameters
		{
			$query = '';
			$q_arr = array();
			if(strpos($uri, '&') > 0) {
				$query = substr($uri, strpos($uri, '&') + 1);
				$uri = substr($uri, 0, strpos($uri, '&'));
				$q_arr = explode('&', $query);
				foreach($q_arr as $q) {
					$qobj = explode('=', $q);
					$q_arr[] = array($qobj[0] => $qobj[1]);
					if(!isset($_GET[$qobj[0]]))
					{
						$_GET[$qobj[0]] = $qobj[1];
					}
				}
			}
		}

		// Check if route is defined without regex

		if (in_array($uri, self::$routes)) {

			$route_pos = array_keys(self::$routes, $uri);

			// Foreach route position
			foreach ($route_pos as $route) {

				if (self::$methods[$route] == $method || self::$methods[$route] == 'ANY') {
					$found_route = true;

					// If route is not an object

					if(!is_object(self::$callbacks[$route])){

						// Call object controller and method

						self::invokeObject(self::$callbacks[$route]);
						if (self::$halts) return;

					} else {

						// Call closure

						call_user_func(self::$callbacks[$route]);
						if (self::$halts) return;

					}
				}

			}

		} else {

			// Check if defined with regex
			$pos = 0;

			// Foreach routes

			foreach (self::$routes as $route) {

				$route = str_replace('//','/',$route);

				if (strpos($route, ':') !== false) {
					$route = str_replace($searches, $replaces, $route);
				}

				if (preg_match('#^' . $route . '$#', $uri, $matched)) {

					if (self::$methods[$pos] == $method || self::$methods[$pos] == 'ANY') {
						$found_route = true; 

						// Remove $matched[0] as [1] is the first parameter.

						array_shift($matched);

						if(!is_object(self::$callbacks[$pos])){

							// Call object controller and method

							self::invokeObject(self::$callbacks[$pos],$matched);
							if (self::$halts) return;

						} else {

							// Call closure
							call_user_func_array(self::$callbacks[$pos], $matched);
							if (self::$halts) return;

						}
					}
				}

				$pos++;
			}
		}

		// Run the error callback if the route was not found

		if (!$found_route) {

			if (!self::$error_callback) {

				self::$error_callback = function() {
					header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
					echo '404';
				};
			} 

			if(!is_object(self::$error_callback)){

				// Call object controller and method
				self::invokeObject(self::$error_callback, null, 'No routes found.');
				if (self::$halts) return;

			} else {

				call_user_func(self::$error_callback);
				if (self::$halts) return;

			}
		}
	}
}