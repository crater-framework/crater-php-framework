<?php
/**
 * Migration Abstract Class
 *
 * @author Dragos Ionita
 * @version 1.0
 * @date 3/24/15
 */

namespace Core\Migration;
use Core\Orm\QueryBuilder;

abstract class MigrationAbstract {

    /**
     * @var null|string Full path of migration files storage
     */
    public $storagePath = null;

    public function __construct(){
        $this->storagePath = dirname(dirname(__DIR__)) . '/App/Data/Migrations';
    }

    /**
     * Query executor
     * @param string $query Query string
     * @return bool
     */
    public function executeQuery($query) {
        $select = new QueryBuilder();
        $select->query($query)->execute();

        return true;
    }
}