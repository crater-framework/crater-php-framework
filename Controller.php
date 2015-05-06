<?php
/**
 * Controller Abstract Class
 *
 * @author Dragos Ionita
 * @version 1.0
 * @date 3/10/15
 */

namespace Core;

use Core\View,
    Core\Language,
    Core\FlashMessage,
    Core\Config as Conf;

abstract class Controller
{

    // View object
    public $view;

    // Language object
    public $language = false;

    // Name of controller
    private $name;

    // App configuration
    public $config;

    // FlashMessage object
    public $flashMessage;

    /**
     * Constructor
     * on run make an instance of the config class, language class and of view class
     */
    public function __construct()
    {

        //initialise config object
        $config = new Conf();
        $this->config = $config->getConfig();

        //initialise the language object
        $defaultLng = $this->config['language_code'];

        if ($defaultLng !== false) {
            $this->language = new Language($defaultLng);
        }

        //initialise the view object
        $this->view = new View();

        //initialise the flash message object
        $this->flashMessage = new FlashMessage();
    }


    /**
     * Get name of controller
     * @return string
     */
    public function getControllerName()
    {
        return $this->name;
    }


    /**
     * Set name of controller
     * @param string $name Name of controller
     */
    public function setControllerName($val)
    {
        $this->name = $val;
    }


    /**
     * Retrieve a member of the $_COOKIE superglobal
     *
     * If no $key is passed, returns the entire $_COOKIE array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getCookie($key = null, $default = null)
    {
        if (null === $key) {
            return $_COOKIE;
        }
        return (isset($_COOKIE[$key])) ? $_COOKIE[$key] : $default;
    }


    /**
     * Retrieve a member of the $_POST superglobal
     *
     * If no $key is passed, returns the entire $_POST array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getPost($key = null, $default = null)
    {
        if (null === $key) {
            return $_POST;
        }
        return (isset($_POST[$key])) ? $_POST[$key] : $default;
    }


    /**
     * Retrieve a member of the $_GET superglobal
     *
     * If no $key is passed, returns the entire $_GET array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getParam($key = null, $default = null)
    {
        if (null === $key) {
            return $_GET;
        }
        return (isset($_GET[$key])) ? $_GET[$key] : $default;
    }


    /**
     * Retrieve a member of the $_SERVER superglobal
     *
     * If no $key is passed, returns the entire $_SERVER array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getServer($key = null, $default = null)
    {
        if (null === $key) {
            return $_SERVER;
        }
        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }


    /**
     * Return the method by which the request was made
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getServer('REQUEST_METHOD');
    }


    /**
     * Was the request made by POST?
     *
     * @return boolean
     */
    public function isPost()
    {
        if ('POST' == $this->getMethod()) {
            return true;
        }
        return false;
    }


    /**
     * Was the request made by GET?
     *
     * @return boolean
     */
    public function isGet()
    {
        if ('GET' == $this->getMethod()) {
            return true;
        }
        return false;
    }


    /**
     * Was the request made by PUT?
     *
     * @return boolean
     */
    public function isPut()
    {
        if ('PUT' == $this->getMethod()) {
            return true;
        }
        return false;
    }


    /**
     * Was the request made by DELETE?
     *
     * @return boolean
     */
    public function isDelete()
    {
        if ('DELETE' == $this->getMethod()) {
            return true;
        }
        return false;
    }


    /**
     * Was the request made by HEAD?
     *
     * @return boolean
     */
    public function isHead()
    {
        if ('HEAD' == $this->getMethod()) {
            return true;
        }
        return false;
    }


    /**
     * Was the request made by OPTIONS?
     *
     * @return boolean
     */
    public function isOptions()
    {
        if ('OPTIONS' == $this->getMethod()) {
            return true;
        }
        return false;
    }


    /**
     * Was the request made by PATCH?
     *
     * @return boolean
     */
    public function isPatch()
    {
        if ('PATCH' == $this->getMethod()) {
            return true;
        }
        return false;
    }


    /**
     * Get the client's IP address
     *
     * @param  boolean $checkProxy
     * @return string
     */
    public function getClientIp($checkProxy = true)
    {
        if ($checkProxy && $this->getServer('HTTP_CLIENT_IP') != null) {
            $ip = $this->getServer('HTTP_CLIENT_IP');
        } else if ($checkProxy && $this->getServer('HTTP_X_FORWARDED_FOR') != null) {
            $ip = $this->getServer('HTTP_X_FORWARDED_FOR');
        } else {
            $ip = $this->getServer('REMOTE_ADDR');
        }
        return $ip;
    }


    /**
     * Redirect to chosen url
     * @param  string $url the url to redirect to
     * @param  boolean $fullpath if true use only url in redirect instead of using DIR
     */
    public static function redirect($url)
    {

        header('Location: ' . $url);
        exit;
    }
}