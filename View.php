<?php
/**
 * Template - View Manager
 *
 * @author Dragos Ionita
 * @version 1.0
 * @date 3/10/15
 */

namespace Core;

use Core\Helpers\Session,
    Core\FlashMessage;

class View
{

    // headers array
    protected $_headers = array();

    // templates path
    protected $templatesDir = 'Templates/';

    // views path
    protected $viewsDir = 'Views/';

    protected $template;
    protected $view;
    protected $vars = array();

    /**
     * Class constructor
     * @param array $vars set data for view
     */
    public function __construct($vars = null)
    {
        $this->vars = $vars;
        $this->template = Session::get('template');
    }


    /**
     * Set template
     * @param string $name Name of template
     * @return boolean
     */
    public function setTemplate($name)
    {
        $this->template = $name;
        return true;
    }


    /**
     * Render view
     * @param string $view path to file from views folder
     */
    public function render($view, array $data = null)
    {
        if (!headers_sent()) {
            foreach ($this->_headers as $header) {
                header($header, true);
            }
        }

        if ($data) {

            if (!empty($this->vars)) {
                // Overlay data config
                $this->vars = array_replace($this->vars, $data);
            } else {
                $this->vars = $data;
            }

        }

        $this->view = $view;
        $path = '../App/' . $this->templatesDir . $this->template . '.phtml';

        if (file_exists($path)) {
            require $path;
        } else {
            die('No template file ' . $this->template . ' present in template directory.');
        }

        return true;
    }


    /**
     * Content function - it is use in template file
     * @return string
     */
    public function content()
    {
        $path = '../App/' . $this->viewsDir . $this->view . '.phtml';
        if (file_exists($path)) {
            require $path;

        } else {
            die('No view file ' . $this->view . ' present in view directory.');
        }
    }

    /**
     * Partial function - rendering a piece of code.
     * @return string
     */
    public function partial($view)
    {
        $path = '../App/' . $this->viewsDir . $view . '.phtml';
        if (file_exists($path)) {
            require $path;

        } else {
            die('No partial view file ' . $view . ' present in view directory.');
        }
    }


    /**
     * Get Flash Message
     * @return mixed
     */
    public function getFlashMessage()
    {
        $flashMessage = new FlashMessage();

        return $flashMessage->getFlashMessage();
    }


    /**
     * Magic set function
     * @param string $name name of data
     * @param mixed $value value of data
     */
    public function __set($name, $value)
    {
        $this->vars[$name] = $value;
    }


    /**
     * Magic get function
     * @param string $name name of data
     * @return mixed
     */
    public function __get($name)
    {
        return $this->vars[$name];
    }


    /**
     * add HTTP header to headers array
     * @param string $header HTTP header text
     */
    public function addheader($header)
    {
        $this->_headers[] = $header;
    }


    /**
     * Add an array with headers to the view
     * @param array $headers
     */
    public function addheaders($headers = array())
    {
        foreach ($headers as $header) {
            $this->addheader($header);
        }
    }


    /**
     * Response with JSON format, without view and template
     * @param array $data array with data
     * @return json
     */
    public function jsonResponse($data)
    {
        $this->_headers[] = 'Content-Type: application/json';

        if (!headers_sent()) {
            foreach ($this->_headers as $header) {
                header($header, true);
            }
        }
        echo json_encode($data);
    }
}