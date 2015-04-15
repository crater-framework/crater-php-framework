<?php
/**
 * Error Class - calls a 404 page
 *
 * @author Dragos Ionita
 * @version 1.0
 * @date 3/10/15
 */

namespace Core;
use Core\Controller,
    Core\View;

class Error extends Controller {

	private $_error = null;

	public function __construct($error){
		parent::__construct();
		$this->_error = $error;
	}


	/**
	 * 404 page Action
	 * load a 404 page with the error message
	 */
	public function indexAction(){

		header("HTTP/1.0 404 Not Found");
		
		$data['title'] = '404';
		$data['error'] = $this->_error;

		$this->view->render('error/404', $data);
	}


	/**
	 * Display errors
	 * @param mixed $errors Error/s array
	 * @param string $class Name of class to apply to div
	 * @return mixed Return div/s
	 */
	public static function display($errors, $class = 'alert alert-danger'){
		if (is_array($errors)){

			foreach($errors as $error){
				$row.= "<div class='$class'>$error</div>";
			}
			return $row;

		} else {

			if(isset($errors)){
				return "<div class='$class'>$errors</div>";
			}

			return false;
		}
	}
}
