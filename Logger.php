<?php
/**
 * Logger Class - Custom errors
 *
 * @author Dragos Ionita
 * @version 1.0
 * @date 3/10/15
 */

namespace Core;

class Logger {

	private static $print_error = true;
	private static $errorFile = '/App/errorlog.html';


	public static function customErrorMsg() {
		echo "<p>An error occured, The error has been reported.</p>";
		exit;
	}


	/**
	 * Saved the exception and calls custom error function
	 * @param exception $e
	 */
	public static function exception_handler($e){

		self::newMessage($e, true);
		self::customErrorMsg();
	}


	/**
	 * Saves error message from exception
	 *
	 * @param int $number Error number
	 * @param string $message The error
	 * @param string $file File originated from
	 * @param int $line Line number
	 */
	public static function error_handler($number, $message, $file, $line){
		$msg = "$message in $file on line $line";
		if ( ($number !== E_NOTICE) && ($number < 2048) ) {
			self::errorMessage($msg, true);
			self::customErrorMsg();
		}
		return 0;
	}


	/**
	 * New exception
	 * @param Exception $exception
	 * @param boolean $print_error Show error or not
	 * @param boolean $clear Clear the errorlog
	 * @param string $error_file File to save to
	 */
	public static function newMessage(Exception $exception, $print_error = false, $clear = false) {
		$message = $exception->getMessage();
		$code = $exception->getCode();
		$file = $exception->getFile();
		$line = $exception->getLine();
		$trace = $exception->getTraceAsString();
		$date = date('M d, Y G:iA');
		$errorFilePath = dirname(__DIR__).self::$errorFile;
		$log_message = "<h3>Exception information:</h3>\n
		<p><strong>Date:</strong> {$date}</p>\n
		<p><strong>Message:</strong> {$message}</p>\n
		<p><strong>Code:</strong> {$code}</p>\n
		<p><strong>File:</strong> {$file}</p>\n
		<p><strong>Line:</strong> {$line}</p>\n
		<h3>Stack trace:</h3>\n
		<pre>{$trace}</pre>\n
		<hr />\n";


		if( is_file($errorFilePath) === false ) {
			file_put_contents($errorFilePath, '');
		}
		if( $clear ) {
			$content = '';
		} else {
			$content = file_get_contents($errorFilePath);
		}
		file_put_contents($errorFilePath, $log_message . $content);
		if($print_error === true){
			echo $log_message;
			exit;
		}
	}

	/**
	 * Custom Error
	 * @param  string  $error The error
	 * @param  boolean $print_error Display error
	 */
	public static function errorMessage($error, $print_error = false) {
		$date = date('M d, Y G:iA');
		$log_message = "<p>Error on $date - $error</p><br>\r\n";

		$errorFilePath = dirname(__DIR__).self::$errorFile;

		if( is_file($errorFilePath) === false ) {
			file_put_contents($errorFilePath, '');
		}

		$content = file_get_contents($errorFilePath);
		file_put_contents($errorFilePath, $log_message . $content);
		if($print_error == true){
			echo $log_message;
			exit;
		}
	}
}