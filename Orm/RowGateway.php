<?php
/**
 * Row Data Gateway Class
 *
 * @author Dragos Ionita
 * @version 1.1
 * @date 3/10/15
 */

namespace Core\Orm;

use Core\Orm\Gateway\RowAbstract;

class RowGateway extends RowAbstract
{

    /**
     * Save row (update)
     * @return $this | bool
     */
    public function save()
    {
        $table = $this->table;
        $params = array();

        $query = "UPDATE {$table->getTableName()} SET ";
        $iValue = 0;

        foreach ($this->_data as $key => $value) {
            if ($key == $table->getPrimaryKey()) {
                continue;
            }

            $query .= "$key = :value$iValue, ";
            $params[":value$iValue"] = $value;
            $iValue++;
        }

        $query = substr($query, 0, -2);
        $pk = $this->_data[$table->getPrimaryKey()];
        $query .= " WHERE {$table->getPrimaryKey()} = $pk";

        return $this->_save($query, $params);
    }


    /**
     * Delete current row
     * @return bool
     */
    public function delete()
    {
        $table = $this->table;
        $pk = $this->_data[$table->getPrimaryKey()];

        $query = "DELETE FROM {$table->getTableName()} WHERE ";
        $query .= "{$table->getPrimaryKey()} = $pk";

        return $this->_delete($query);
    }


    /**
     * Get table
     * @return \Core\Orm\TableGateway
     */
    public function getTable()
    {
        return $this->table;
    }
}