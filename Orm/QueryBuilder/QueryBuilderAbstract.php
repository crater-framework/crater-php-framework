<?php
/**
 * Abstract Class for Query Builder
 *
 * @author Dragos Ionita
 * @version 1.0
 * @date 3/10/15
 */

namespace Core\Orm\QueryBuilder;

use Core\Orm\Adapter;

abstract class QueryBuilderAbstract
{

    // Db adapter
    protected $db;

    protected $q = array();

    /**
     * Class Constructor
     */
    public function __construct()
    {
        $this->db = Adapter::get();
    }

    public function makeQuery()
    {
        $data = $this->q;

        $params = array();
        $query = "SELECT ";

        if (isset($data['from']['columns'])) {
            foreach ($data['from']['columns'] as $key => $value) {
                $query .= "$value, ";
            }

            $query = substr($query, 0, -2);
        } else {
            $query .= "{$data['from']['table']}.* ";
        }

        // Join columns
        if (isset($data['join'])) {
            foreach ($data['join'] as $join) {
                if (isset($join['columns'])) {
                    foreach ($join['columns'] as $col) {
                        $query .= ", {$join['table']}.{$col}";
                    }
                } else {
                    $query .= ", {$join['table']}.*";
                }

            }
        }

        $query .= " FROM {$data['from']['table']}";

        // Join
        if (isset($data['join'])) {
            foreach ($data['join'] as $join) {
                $query .= " JOIN {$join['table']} ON {$join['conditions']}";

            }
        }

        if (isset($data['where'])) {
            $query .= " WHERE (";

            $iWhere = 0;
            foreach ($data['where'] as $key => $value) {
                if (!is_int($key)) {
                    $query .= "$key = :where$iWhere AND ";
                    $params[":where$iWhere"] = $value;
                    $iWhere++;
                } else {
                    $query .= "$value AND";
                }

            }

            $query = substr($query, 0, -4);

            $iWhereO = 0;
            if (isset($data['orwhere'])) {
                foreach ($data['orwhere'] as $key => $value) {
                    if (!is_int($key)) {
                        $query .= " OR $key = :whereO$iWhereO ";
                        $params[":whereO$iWhereO"] = $value;
                        $iWhereO++;
                    } else {
                        $query .= "OR $value ";
                    }
                }
            }
            $query .= ")";
        }

        if (isset($data['group'])) {
            $query .= " GROUP BY :group";
            $params[':group'] = $data['group'];
        }

        $iOrder = 0;
        if (isset($data['order'])) {
            $query .= " ORDER BY ";
            foreach ($data['order'] as $key => $value) {
                $query .= ":order$iOrder $value";
                $params[":order$iOrder"] = $key;
                $iOrder++;
            }
        }

        if (isset($data['limit'])) {
            $query .= " LIMIT :limit";
            $params[":limit"] = $data['limit']['limit'];
            if (isset($data['limit']['offset'])) {
                $query .= ", :offset";
                $params[":offset"] = $data['limit']['offset'];
            }
        }

        return ['query' => $query, 'params' => $params];
    }

    public function execute()
    {
        $db = $this->db;

        $qp = array();

        if (isset($this->q['query'])) {
            $sth = $db->prepare($this->q['query']);
        } else {
            $qp = $this->makeQuery();
            $sth = $db->prepare($qp['query']);
            foreach ($qp['params'] as $key => $val) {
                if (is_int($val)) {
                    $sth->bindValue("$key", $val, $db::PARAM_INT);
                } else {
                    $sth->bindValue("$key", $val);
                }
            }
        }

        $sth->execute();

        return $sth;
    }

    public function fetchAll()
    {
        $sth = $this->execute();
        $db = $this->db;

        return $sth->fetchAll($db::FETCH_ASSOC);
    }

    public function fetchRow()
    {
        $sth = $this->execute();
        $db = $this->db;

        return $sth->fetch($db::FETCH_ASSOC);
    }
}