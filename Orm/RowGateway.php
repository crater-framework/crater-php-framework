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
     * @return $this
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
        $valId = $this->_data[$table->getPrimaryKey()];
        $query .= " WHERE {$table->getPrimaryKey()} = $valId";

        return $this->_save($query, $params);
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