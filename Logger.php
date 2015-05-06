<?php
/**
 * Logger Class - Custom errors
 *
 * @author Dragos Ionita
 * @version 1.0
 * @date 3/10/15
 */

namespace Core;

class Logger
{

    private static $printError = true;
    private static $errorFile = '/App/Data/errorlog.html';


    public static function customErrorMsg()
    {
        echo "<p>An error occured, The error has been reported.</p>";
        exit;
    }


    /**
     * Saved the exception and calls custom error function
     * @param exception $e
     */
    public static function exception_handler($e)
    {

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
    public static function error_handler($number, $message, $file, $line)
    {
        $msg = "$message in $file on line $line";
        if (($number !== E_NOTICE) && ($number < 2048)) {
            self::errorMessage($msg, true);
            self::customErrorMsg();
        }
        return 0;
    }


    /**
     * New exception
     * @param Exception $exception
     * @param boolean $printError Show error or not
     * @param boolean $clear Clear the errorlog
     * @param string $error_file File to save to
     */
    public static function newMessage(Exception $exception, $printError = false, $clear = false)
    {
        $message = $exception->getMessage();
        $code = $exception->getCode();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();
        $date = date('M d, Y G:iA');
        $errorFilePath = dirname(dirname(dirname(__DIR__))) . self::$errorFile;
        $logMessage = "<h3>Exception information:</h3>\n
		<p><strong>Date:</strong> {$date}</p>\n
		<p><strong>Message:</strong> {$message}</p>\n
		<p><strong>Code:</strong> {$code}</p>\n
		<p><strong>File:</strong> {$file}</p>\n
		<p><strong>Line:</strong> {$line}</p>\n
		<h3>Stack trace:</h3>\n
		<pre>{$trace}</pre>\n
		<hr />\n";


        if (is_file($errorFilePath) === false) {
            file_put_contents($errorFilePath, '');
        }
        if ($clear) {
            $content = '';
        } else {
            $content = file_get_contents($errorFilePath);
        }
        file_put_contents($errorFilePath, $logMessage . $content);
        if ($printError === true) {
            echo $logMessage;
            exit;
        }
    }

    /**
     * Custom Error
     * @param  string $error The error
     * @param  boolean $printError Display error
     */
    public static function errorMessage($error, $printError = false)
    {
        $date = date('M d, Y G:iA');
        $logMessage = "<p>Error on $date - $error</p><br>\r\n";

        $errorFilePath = dirname(dirname(dirname(__DIR__))) . self::$errorFile;

        if (is_file($errorFilePath) === false) {
            file_put_contents($errorFilePath, '');
        }

        $content = file_get_contents($errorFilePath);
        file_put_contents($errorFilePath, $logMessage . $content);
        if ($printError == true) {
            echo $logMessage;
            exit;
        }
    }
}