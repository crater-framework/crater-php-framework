<?php
/**
 * Table Data Gateway Class
 *
 * @author Dragos Ionita
 * @version 1.1
 * @date 3/10/15
 */

namespace Core\Orm;

use Core\Orm\Gateway\TableAbstract;

class TableGateway extends TableAbstract
{

    /**
     * Create new row
     * @param array $data Array with data ('column_name' => 'foo')
     * @return \Core\Orm\RowGateway | bool
     */
    public function insert(array $data)
    {
        $params = array();
        $query = "INSERT INTO $this->_name (";

        foreach ($data as $key => $value) {
            $query .= "$key, ";

        }

        $query = substr($query, 0, -2);
        $query .= ") VALUES (";

        $iValue = 0;
        foreach ($data as $key => $value) {
            $query .= ":value$iValue, ";
            $params[":value$iValue"] = $value;
            $iValue++;
        }

        $query = substr($query, 0, -2);
        $query .= ")";

        return $this->_insert($query, $params);
    }


    /**
     * Find a row by primary key
     * @param mixed $primaryKey Primary key value
     * @return \Core\Orm\RowGateway | bool
     */
    public function find($primaryKey)
    {
        $where = array(
            $this->_primary => $primaryKey
        );

        $qp = $this->_fetch($where);
        $row = $this->_fetchRow($qp['query'], $qp['params']);
        $className = $this->_rowClass;
        $class = new $className($this);

        if (!$row) {
            return false;
        }

        $class->setData($row);

        return $class;
    }


    /**
     * Fetch one row
     * @param array $where Where conditions array
     * @param array $column Array with columns that you want.
     * @param array $order Array with order condition ('first_name, last_name' => 'DESC')
     * @return \Core\Orm\RowGateway
     */
    public function fetchRow(array $where = null, array $column = null, array $order = null)
    {
        $qp = $this->_fetch($where, $column, $order);

        $row = $this->_fetchRow($qp['query'], $qp['params']);
        $className = $this->_rowClass;
        $class = new $className($this);

        if (!$row) {
            return false;
        }

        $class->setData($row);

        return $class;
    }


    /**
     * Fetch all rows
     * @param array $where Where conditions array
     * @param array $column Array with columns that you want.
     * @param array $order Array with order condition ('first_name, last_name' => 'DESC')
     * @param string $limit String with limit value
     * @param bool $lazy Lazy return. If this is true, the response will be an array
     * @return array | bool
     */
    public function fetchAll(array $where = null, array $column = null, array $order = null, $limit = null, $lazy = false)
    {
        $qp = $this->_fetch($where, $column, $order, $limit);
        $rows = $this->_fetchAll($qp['query'], $qp['params']);

        if (!$rows) {
            return false;
        }

        if (!$lazy) {
            $hydratedRows = array();
            foreach ($rows as $id => $row) {
                $className = $this->_rowClass;
                $class = new $className($this);
                $class->setData($row);
                $hydratedRows[$id] = $class;
            }

            return $hydratedRows;
        }
        return $rows;
    }


    /**
     * Delete a row or rowset
     * @param array $where
     * @return bool
     */
    public function delete(array $where)
    {
        if (!$where) {
            die ('Where is required!');
        }

        return $this->_delete($where);
    }


    /**
     * Update a row or rowset
     *
     * @param array $data Data array
     * @param array $where Where conditions array
     * @return bool
     */
    public function update(array $data, array $where)
    {
        if (!$where) {
            die ('Where is required');
        }

        if (!$data) {
            die ('Data is required');
        }

        return $this->_update($data, $where);
    }
}