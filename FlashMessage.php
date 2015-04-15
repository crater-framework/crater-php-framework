<?php
/**
 * Flash Message Library
 *
 * @author Dragos Ionita
 * @version 1.0
 * @date 3/20/15
 */

namespace Core;
use Core\Helpers\Session;

class FlashMessage {

    /**
     * Create info flash message
     * @param string $message
     */
    public function info($message) {
        Session::set(array('FMess' => true, 'FMessType' => 'info', 'FMessMsg' => $message));
    }


    /**
     * Create success flash message
     * @param string $message
     */
    public function success($message) {
        Session::set(array('FMess' => true, 'FMessType' => 'success', 'FMessMsg' => $message));
    }


    /**
     * Create error flash message
     * @param string $message
     */
    public function error($message) {
        Session::set(array('FMess' => true, 'FMessType' => 'error', 'FMessMsg' => $message));
    }


    /**
     * Create warning flash message
     * @param string $message
     */
    public function warning($message) {
        Session::set(array('FMess' => true, 'FMessType' => 'warning', 'FMessMsg' => $message));
    }

    /**
     * Create info flash message
     * @return array|bool
     */
    public function getFlashMessage() {
        if (Session::get('FMess') === true) {

            $response = array (
                'type' => Session::get('FMessType'),
                'message' => Session::get('FMessMsg')
            );
            Session::destroy('FMess');
            Session::destroy('FMessType');
            Session::destroy('FMessMsg');

            return $response;
        }
        return false;
    }
}