<?php
/**
 * Abstract Class for Row Data Gateway
 *
 * @author Dragos Ionita
 * @version 1.1
 * @date 3/10/15
 */

namespace Core\Orm\Gateway;

abstract class RowAbstract {

    // All data of record
    protected $_data;
    // Parent table
    protected $table;
    // Db adapter
    protected $db;

    /**
     * Class Constructor
     * @param \Core\Orm\TableGateway $table Set parent table
     * @param mixed $primaryKey Set primary key
     */
    public function __construct(\Core\Orm\TableGateway $table, $primaryKey = null) {
        $this->table = $table;
        $this->db = $table->getAdapter();

        if ($primaryKey) $this->_data = $this->_getData($primaryKey);
    }


    /**
     * Populate row data
     * @param mixed $primaryKey
     * @return mixed
     */
    private function _getData($primaryKey) {
        $table = $this->table;
        $db = $this->db;

        $query = "SELECT * FROM {$table->getTableName()} WHERE {$table->getPrimaryKey()} = $primaryKey";
        $sth = $table->getAdapter()->prepare($query);
        $sth->execute();
        return $sth->fetch($db::FETCH_ASSOC);
    }


    /**
     * Set data function
     * @param array $data Array with record data
     */
    public function setData(array $data) {
        $this->_data = $data;
    }


    public function __set($key, $value) {
        $table = $this->table;

        if ($key == $table->getPrimaryKey())
            die('You can\'t change primary key');

        if (!in_array($key, $table->getTableColumns()))
            die("You don't have '$key' column");

        $this->_data[$key] = $value;

        return $this;
    }


    public function __get($key) {
        $table = $this->table;
        if (!in_array($key, $table->getTableColumns()))
            die("You don't have '$key' column");

        return $this->_data[$key];
    }


    public function __call($method, $args) {
        return $this->checkRelationship($method);
    }


    /**
     * Parent of save() function
     * @param string $query Query string
     * @return mixed|bool
     */
    protected function _save($query, $params) {
        $db = $this->db;
        $sth = $db->prepare($query);
        foreach ($params as $key => $val) {
            if(is_int($val)){
                $sth->bindValue("$key", $val, $db::PARAM_INT);
            } else {
                $sth->bindValue("$key", $val);
            }
        }
        $response = $sth->execute();
        if ($response) return $this;
        return false;
    }


    /**
     * Check if exist relationship alias
     * @param string @method
     * @return mixed
     */
    private function checkRelationship($method) {
        $table = $this->table;

        $one = $table->getOneRelationship();
        $many = $table->getManyRelationship();
        $belongsTo = $table->getBelongsToRelationship();

        $type = null;
        $params = null;

        if (!is_null($one) && array_key_exists($method, $one)) {
            $type = 'one';
            $params = $one[$method];
        }

        if (!is_null($many) && array_key_exists($method, $many)) {
            $type = 'many';
            $params = $many[$method];
        }

        if (!is_null($belongsTo) &&array_key_exists($method, $belongsTo)) {
            $type = 'belongsTo';
            $params = $belongsTo[$method];
        }

        if (is_null($type)) return false;

        return $this->parseRelationship($table, $type, $params);
    }


    /**
     * Parse relationship function (if exist :D)
     * @param \Core\Orm\TableGateway $table Current row table
     * @param string $type Type of relationship (one, many, belongsTo)
     * @param array $params Relationship definition
     */
    private function parseRelationship($table, $type, $params) {

        $tableClass = $params['refTableClass'];
        $refTable = new $tableClass();

        $where = array(
            $params['fkColumn'] => $this->_data[$table->getPrimaryKey()]
        );

        if ($type == 'many') {

            $rows = $refTable->fetchAll($where);
            return $rows;
        }

        if ($type == 'one') {
            $row = $refTable->fetchRow($where);
            return $row;
        }

        if ($type == 'belongsTo') {

            $row = $refTable->find($this->_data[$table->getPrimaryKey()]);
            return $row;
        }

        return false;
    }
}