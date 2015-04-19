<?php
/*
 * Abstract Class for Table Data Gateway
 *
 * @author Dragos Ionita
 * @version 1.1
 * @date 3/10/15
 */

namespace Core\Orm\Gateway;

use Core\Orm\Adapter;

abstract class TableAbstract
{

    //Db adapter
    protected $db;

    //Table name
    protected $_name;

    //Primary key of table
    protected $_primary;

    //Row Class
    protected $_rowClass;

    //Table columns
    protected $_columns;

    //Relationship arrays
    protected $_has_many;
    protected $_has_one;
    protected $_belongs_to;


    public function __construct()
    {
        $this->db = Adapter::get();
        $this->_setTableColumns();
    }


    /*
     * Get name of table
     * @return string Name of table
     */
    public function getTableName()
    {
        return $this->_name;
    }


    /*
     * Get primary key column
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->_primary;
    }


    /*
     * Get table columns
     * @return array
     */
    public function getTableColumns()
    {
        return $this->_columns;
    }


    /*
     * Get Db adapter
     * @return mixed
     */
    public function getAdapter()
    {
        return $this->db;
    }


    /*
     * Get OneToOne relationships
     */
    public function getOneRelationship()
    {
        return $this->_has_one;
    }


    /*
     * Get ManyToMany relationships
     */
    public function getManyRelationship()
    {
        return $this->_has_many;
    }


    /*
     * Get BelongsTo relationships
     */
    public function getBelongsToRelationship()
    {
        return $this->_belongs_to;
    }


    /*
     * Retrieve and set table columns
     */
    private function _setTableColumns()
    {
        $db = $this->db;
        $q = $db->prepare("DESCRIBE " . $this->_name);
        $q->execute();
        $this->_columns = $q->fetchAll($db::FETCH_COLUMN);
    }


    /*
     * Main query string generator
     *
     * @param array $where Where conditions array
     * @param array $column Array with columns that you want.
     * @param array $order Array with order condition ('first_name, last_name' => 'DESC')
     * @param string $limit String with limit value
     * @return string
     */
    protected function _fetch(array $where = null, array $column = null, $order = null, $limit = null)
    {
        $params = array();
        $query = 'SELECT ';
        if ($column) {
            foreach ($column as $name) {
                $query .= "$name, ";
            }
            $query = substr($query, 0, -2);
        } else {
            $query .= "*";
        }

        $query .= " FROM $this->_name";
        if ($where) {
            $iWhere = 0;
            $query .= " WHERE (";
            foreach ($where as $key => $value) {
                $query .= "$key = :where$iWhere AND ";
                $params[":where$iWhere"] = $value;
                $iWhere++;
            }
            $query = substr($query, 0, -4);
            $query .= ")";
        }

        if ($order) {
            $query .= " ORDER BY ";
            foreach ($order as $key => $value) {
                $query .= "$key $value";
            }
        }

        if ($limit) {
            $query .= " LIMIT $limit";
        }

        return array('query' => $query, 'params' => $params);
    }


    /*
     * Parent of fetchRow() function
     * @param string $query Query string
     */
    protected function _fetchRow($query, $params)
    {
        $db = $this->db;

        $sth = $db->prepare($query);
        foreach ($params as $key => $val) {
            if (is_int($val)) {
                $sth->bindValue("$key", $val, $db::PARAM_INT);
            } else {
                $sth->bindValue("$key", $val);
            }
        }
        $sth->execute();

        return $sth->fetch($db::FETCH_ASSOC);
    }


    /*
     * Parent of fetchAll() function
     * @param string $query Query string
     */
    protected function _fetchAll($query, $params)
    {
        $db = $this->db;
        $sth = $db->prepare($query);
        foreach ($params as $key => $val) {
            if (is_int($val)) {
                $sth->bindValue("$key", $val, $db::PARAM_INT);
            } else {
                $sth->bindValue("$key", $val);
            }
        }
        $sth->execute();
        return $sth->fetchAll($db::FETCH_ASSOC);
    }


    /*
     * Parent of insert() function
     * @param string $query Query string
     */
    protected function _insert($query, $params)
    {
        $db = $this->db;
        $sth = $db->prepare($query);

        foreach ($params as $key => $val) {
            if (is_int($val)) {
                $sth->bindValue("$key", $val, $db::PARAM_INT);
            } else {
                $sth->bindValue("$key", $val);
            }
        }

        $response = $sth->execute();

        if ($response) {

            $class = $this->_rowClass;
            $row = new $class($this, $db->lastInsertId());

            return $row;
        }
        return false;
    }
}