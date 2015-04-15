<?php
/**
 * Select Class
 *
 * @author Dragos Ionita
 * @version 1.1
 * @date 3/30/15
 */

namespace Core\Orm;
use Core\Orm\QueryBuilder\QueryBuilderAbstract;

class QueryBuilder extends QueryBuilderAbstract {

    public function from($table, array $columns = null) {
        $this->q['from']['table'] = $table;
        if (isset($columns))
            $this->q['from']['columns'] = $columns;

        return $this;
    }

    public function query($query) {
        $this->q['query'] = $query;
        return $this;
    }

    public function where(array $where) {
        $this->q['where'] = $where;

        return $this;
    }

    public function orWhere(array $where) {
        $this->q['orwhere'] = $where;

        return $this;
    }

    public function join($table, $joinCondition, array $columns = null) {

        $join = array('table' => $table, 'conditions' => $joinCondition);

        if (isset($columns))
            $join['columns'] = $columns;

        $this->q['join'][] = $join;

        return $this;
    }

    public function limit($limit, $offset = null) {
        $this->q['limit']['limit'] = $limit;
        if (isset($offset))
            $this->q['limit']['offset'] = $offset;

        return $this;
    }

    public function order(array $order) {
        $this->q['order'] = $order;

        return $this;
    }

    public function group($column) {
        $this->q['group'] = $column;

        return $this;
    }
}