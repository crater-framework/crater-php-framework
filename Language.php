<?php
/**
 * Language Class - translate front-end strings
 *
 * @author Dragos Ionita
 * @version 1.0
 * @date 3/4/15
 */

namespace Core;

class Language {

	// Array with language
	private $_array;
	private static $_languageCode;


	/**
	 * Constructor
	 * @param string $language_code Language code
	 */
	public function __construct($language_code) {
		self::$_languageCode = $language_code;
	}


	/**
	 * Load language
	 * @param string $name Key of string
	 * @param string $code Language code
	 */
	public function load($name, $code = null) {

		if (is_null($code) or !$code) $code = self::$_languageCode;

		// Lang file
		$file = dirname(__DIR__) . "/App/Language/$code/$name.php";

		// Check if is readable
		if(is_readable($file)){

			// Require file
			$this->_array = include($file);

		} else {

			// Display error
			echo \Core\Error::display("Could not load language file '$code/$name.php'");
			die;

		}

	}


	/**
	 * Get element from language array by key
	 * @param string $value Key of string
	 * @return string
	 */
	public function get($value) {

		if(!empty($this->_array[$value])){
			return $this->_array[$value];
		} else {
			return $value;
		}

	}


	/**
	 * Get language for views
	 * @param  string $value this is "word" value from language file
	 * @param  string $name  name of file with language
	 * @param  string $code  optional, language code
	 * @return string
	 */
	public static function show($value, $name, $code = null) {

		if (is_null($code)) $code = self::$_languageCode;

		// Lang file
		$file = dirname(__DIR__) . "/App/Language/$code/$name.php";

		// Check if is readable
		if(is_readable($file)){

			// Require file
			$_array = include($file);

		} else {

			// Display error
			echo \Core\Error::display("Could not load language file '$code/$name.php'");
			die;

		}

		if(!empty($_array[$value])){
			return $_array[$value];
		} else {
			return $value;
		}
	}

}
