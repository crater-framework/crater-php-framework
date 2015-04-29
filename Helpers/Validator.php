<?php
/**
 * Validators Helper
 *
 * @author Dragos Ionita
 * @version 1.1
 * @date 3/06/15
 */

namespace Core\Helpers;

class Validator
{
    public static $expression = Array(
        'date' => "^[0-9]{4}[-/][0-9]{1,2}[-/][0-9]{1,2}\$",
        'amount' => "^[-]?[0-9]+\$",
        'number' => "^[-]?[0-9,]+\$",
        'alfanum' => "^[0-9a-zA-Z ,.-_\\s\?\!]+\$",
        'not_empty' => "[a-z0-9A-Z]+",
        'words' => "^[A-Za-z]+[A-Za-z \\s]*\$",
        'phone' => "^[0-9]{10,11}\$",
        'zipcode' => "^[1-9][0-9]{3}[a-zA-Z]{2}\$",
        'plate' => "^([0-9a-zA-Z]{2}[-]){2}[0-9a-zA-Z]{2}\$",
        'price' => "^[0-9.,]*(([.,][-])|([.,][0-9]{2}))?\$",
        '2digitopt' => "^\d+(\,\d{2})?\$",
        '2digitforce' => "^\d+\,\d\d\$",
        'anything' => "^[\d\D]{1,}\$"
    );

    private $validation,
        $sanatation,
        $required,
        $errors,
        $corrects,
        $fields;


    public function __construct($validation = array(), $required = array(), $sanatation = array())
    {
        $this->validation = $validation;
        $this->sanatation = $sanatation;
        $this->required = $required;
        $this->errors = array();
        $this->corrects = array();
    }


    /**
     * Validates an array of items (if needed) and returns true or false
     *
     * @param array $items
     * @return bool
     */
    public function validate(array $items)
    {
        $this->fields = $items;
        $haveFailures = false;
        foreach ($items as $key => $val) {
            if ((strlen($val) == 0 || array_search($key, $this->validation) === false) && array_search($key, $this->required) === false) {
                $this->corrects[] = $key;
                continue;
            }
            $result = self::validateItem($val, $this->validation[$key]);
            if ($result === false) {
                $haveFailures = true;
                $this->addError($key, $this->validation[$key]);
            } else {
                $this->corrects[] = $key;
            }
        }

        return (!$haveFailures);
    }


    /**
     * Return errors
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }


    /**
     * Return corrects data
     * @return array
     */
    public function getData() {
        return $this->corrects;
    }


    /**
     * Sanatizes an array of items according to the $this->sanatation
     *
     * example: $sanatation = array('fieldname', 'otherfieldname'=>'float');
     * @param array $items
     * @return mixed
     */
    public function sanatize($items)
    {
        foreach ($items as $key => $val) {
            if (array_search($key, $this->sanatation) === false && !array_key_exists($key, $this->sanatation)) continue;
            $items[$key] = self::sanatizeItem($val, $this->validation[$key]);
        }

        return ($items);
    }


    /**
     * Adds an error to the errors array.
     * @param $field
     * @param string $type
     */
    private function addError($field, $type = 'string')
    {
        $this->errors[$field] = $type;
    }


    /**
     * Sanatize a single var according to $type.
     * Allows for static calling to allow simple sanatization
     *
     * @param $var
     * @param $type
     * @return mixed
     */
    public static function sanatizeItem($var, $type)
    {
        $flags = NULL;
        switch ($type) {
            case 'url':
                $filter = FILTER_SANITIZE_URL;
                break;
            case 'int':
                $filter = FILTER_SANITIZE_NUMBER_INT;
                break;
            case 'float':
                $filter = FILTER_SANITIZE_NUMBER_FLOAT;
                $flags = FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND;
                break;
            case 'email':
                $var = substr($var, 0, 254);
                $filter = FILTER_SANITIZE_EMAIL;
                break;
            case 'string':
            default:
                $filter = FILTER_SANITIZE_STRING;
                $flags = FILTER_FLAG_NO_ENCODE_QUOTES;
                break;

        }
        $output = filter_var($var, $filter, $flags);
        return ($output);
    }


    /**
     * Validates a single var according to $type.
     * Allows for static calling to allow simple validation.
     *
     * @param $var
     * @param $type
     * @return bool
     */
    public static function validateItem($var, $type)
    {
        if (array_key_exists($type, self::$expression)) {
            $returnVal = filter_var($var, FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => '!' . self::$expression[$type] . '!i'))) !== false;
            
            return ($returnVal);
        }
        
        $filter = false;
        switch ($type) {
            case 'email':
                $var = substr($var, 0, 254);
                $filter = FILTER_VALIDATE_EMAIL;
                break;
            case 'int':
                $filter = FILTER_VALIDATE_INT;
                break;
            case 'boolean':
                $filter = FILTER_VALIDATE_BOOLEAN;
                break;
            case 'ip':
                $filter = FILTER_VALIDATE_IP;
                break;
            case 'url':
                $filter = FILTER_VALIDATE_URL;
                break;
        }

        return ($filter === false) ? false : filter_var($var, $filter) !== false ? true : false;
    }
}