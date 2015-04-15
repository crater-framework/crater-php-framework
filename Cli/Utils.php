<?php
/**
 * Cli Utils Class
 *
 * @author Dragos Ionita
 * @version 1.0
 * @date 3/24/15
 */

namespace Core\Cli;

class Utils {

    /**
     * Set background color of text
     * @param string $text
     * @param string $status
     * @return string
     * @throws Exception
     */
    public static function colorize($text, $status) {
        $out = "";
        switch($status) {
            case "SUCCESS":
                $out = "[42m"; //Green background
                break;
            case "FAILURE":
                $out = "[41m"; //Red background
                break;
            case "WARNING":
                $out = "[43m"; //Yellow background
                break;
            case "NOTE":
                $out = "[44m"; //Blue background
                break;
            default:
                throw new Exception("Invalid status: " . $status);
        }
        return chr(27) . "$out" . "$text" . chr(27) . "[0m" . PHP_EOL;
    }
}