<?php
/**
 * Migration Class
 *
 * @author Dragos Ionita
 * @version 1.0
 * @date 9/4/15
 */

namespace Core;

use Core\Migration\MigrationAbstract;

class Migration extends MigrationAbstract
{

    /**
     * Create new migration file
     * @param string $name Name of migration file
     * @return bool|string
     */
    public function newMigration($name)
    {
        $time = time();
        $date = date("n/j/Y");

        $fileName = "{$time}_migration.php";
        if ($name) $fileName = "{$time}_{$name}.php";

        $file = $this->storagePath . "/" . $fileName;
        $class = 'Migration_' . ((int)$time);
        $contents = array('<?php');
        $contents[] = '/*';
        $contents[] = " * {$name} migration file";
        $contents[] = ' *';
        $contents[] = ' * @author ';
        $contents[] = ' * @version 1.0';
        $contents[] = " * @date $date";
        $contents[] = ' */';
        $contents[] = 'class ' . $class . ' extends \Core\Migration';
        $contents[] = '{';
        $contents[] = '';
        $contents[] = "    public function up()";
        $contents[] = "    {";
        $contents[] = "";
        $contents[] = "    }";
        $contents[] = "";
        $contents[] = "    public function down()";
        $contents[] = "    {";
        $contents[] = "";
        $contents[] = "    }";
        $contents[] = '}';

        if (file_put_contents($file, implode("\n", $contents))) {
            echo 'Create ' . $file . "\n\r";
            return $file;
        }
        return false;
    }


    /**
     * Create new table
     * @param string $name Name of table
     * @param array $data Array with table definition
     * @return bool
     */
    public function createTable($name, $data)
    {
        $q = "CREATE TABLE {$name} (";
        foreach ($data as $key => $v) {
            $q .= $key;

            if (isset($v['type'])) {
                $q .= " {$v['type']}";
                if (isset($v['length'])) {
                    $q .= "({$v['length']})";
                } else {
                    if ($v['type'] != 'int' && $v['type'] != 'datetime') {
                        die ("Please set length for table: {$name} - column: {$key}");
                    }
                }

                if (isset($v['unsigned']) && $v['unsigned'] == true) {
                    $q .= " UNSIGNED";
                }

                if (!isset($v['null']) || $v['null'] == false) {
                    $q .= " NOT NULL";
                }

                if (isset($v['default'])) {
                    if (is_string($v['default'])) {
                        $v['default'] = "'{$v['default']}'";
                    }

                    $q .= " DEFAULT {$v['default']}";
                }

                if (isset($v['ai']) && $v['ai'] == true) {
                    $q .= " AUTO_INCREMENT";
                }

                if ((isset($v['unique']) && $v['unique'] == true) && (isset($v['primary']) && $v['primary'] == true)) {
                    die ("Unique or Primary?");
                }

                if (isset($v['unique']) && $v['unique'] == true) {
                    $q .= " UNIQUE";
                }

                if (isset($v['primary']) && $v['primary'] == true) {
                    $q .= " PRIMARY KEY";
                }

                if (isset($v['foreign']) && isset($v['foreign']['table']) && isset($v['foreign']['column'])) {
                    $q .= " ,FOREIGN KEY ({$key}) REFERENCES {$v['foreign']['table']}({$v['foreign']['column']})";

                    if (isset($v['foreign']['delete']) && $v['foreign']['delete'] == true) {
                        $q .= " ON DELETE CASCADE";
                    }

                    if (isset($v['foreign']['update']) && $v['foreign']['update'] == true) {
                        $q .= " ON UPDATE CASCADE";
                    }
                }

                $q .= ", ";

            } else {
                die ("Please set type of column ");
            }
        }

        $q = substr($q, 0, -2);
        $q .= ")";

        return $this->executeQuery($q);
    }


    /**
     * Custom query function
     * @param string $query Query
     * @return bool
     */
    public function query($query)
    {
        return $this->executeQuery($query);
    }


    /**
     * Add column
     * @param string $table Table name
     * @param string $name Column name
     * @param array $data Array with column definition
     * @return bool
     */
    public function addColumn($table, $name, $data)
    {

        $q = "ALTER TABLE {$table} ADD {$name}";
        $v = $data;

        if (isset($v['type'])) {
            $q .= " {$v['type']}";
            if (isset($v['length'])) {
                $q .= "({$v['length']})";
            } else {
                if ($v['type'] != 'int' && $v['type'] != 'datetime') {
                    die ("Please set length for table: {$name} - column: {$name}");
                }
            }

            if (isset($v['unsigned']) && $v['unsigned'] == true) {
                $q .= " UNSIGNED";
            }

            if (!isset($v['null']) || $v['null'] == false) {
                $q .= " NOT NULL";
            }

            if (isset($v['default'])) {
                if (is_string($v['default'])) {
                    $v['default'] = "'{$v['default']}'";
                }

                $q .= " DEFAULT {$v['default']}";
            }

            if (isset($v['ai']) && $v['ai'] == true) {
                $q .= " AUTO_INCREMENT";
            }

            if ((isset($v['unique']) && $v['unique'] == true) && (isset($v['primary']) && $v['primary'] == true)) {
                die ("Unique or Primary?");
            }

            if (isset($v['unique']) && $v['unique'] == true) {
                $q .= " UNIQUE";
            }

            if (isset($v['primary']) && $v['primary'] == true) {
                $q .= " PRIMARY KEY";
            }

            if (isset($v['after'])) {
                $q .= " AFTER {$v['after']}";
            }

        } else {
            die ("Please set type of column ");
        }

        return $this->executeQuery($q);
    }


    /**
     * Drop column
     * @param string $table Table name
     * @param string $name Column name
     * @return bool
     */
    public function dropColumn($table, $name)
    {
        $q = "ALTER TABLE {$table} DROP COLUMN {$name}";

        return $this->executeQuery($q);
    }


    /**
     * Drop table
     * @param string $name Table name
     * @return bool
     */
    public function dropTable($name)
    {
        $q = "DROP TABLE {$name}";

        return $this->executeQuery($q);
    }


    /**
     * Add index
     * @param string $table Table name
     * @param string $column Column name
     * @return bool
     */
    public function addIndex($table, $column)
    {
        $q = "ALTER TABLE {$table} ADD INDEX ({$column})";

        return $this->executeQuery($q);
    }
}