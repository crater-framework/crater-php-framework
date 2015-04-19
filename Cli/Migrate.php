<?php
/**
 * Cli Migration Class
 *
 * @author Dragos Ionita
 * @version 1.0
 * @date 3/24/15
 */

namespace Core\Cli;

use Core\Migration,
    Core\Cli\Utils,
    Core\Config,
    Core\Orm\QueryBuilder;

class Migrate
{

    /**
     * @var array $arguments Command line arguments
     */
    protected $arguments;

    protected $migrateManager;
    public $table;

    public function __construct($arguments)
    {

        $config = new Config();
        $this->table = (isset($config->getConfig()['settings_table'])) ? $config->getConfig()['settings_table'] : "crt_settings";

        array_shift($arguments);
        $this->arguments = $arguments;

        $this->migrateManager = new Migration();

        if (count($this->arguments) == 0) Screen::migrateMain();
        if ($this->arguments[0] == 'new') $this->newMigration();
        if ($this->arguments[0] == 'apply') $this->apply();
        if ($this->arguments[0] == 'rollback') {
            if (isset($this->arguments[1]) && is_numeric($this->arguments[1])) {
                $this->rollback($this->arguments[1]);
            } else {
                Screen::rollbackNoVersion();
            }
        }
        if ($this->arguments[0] == 'init') $this->init();
    }

    /**
     * Generate new migration file
     */
    public function newMigration()
    {
        $fileName = (isset($this->arguments[1])) ? $this->arguments[1] : null;
        $this->migrateManager->newMigration($fileName);
    }


    /**
     * Initialize migration
     * @throws Exception
     */
    public function init()
    {
        $select = new QueryBuilder();

        $select
            ->query("CREATE TABLE IF NOT EXISTS {$this->table} (id int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, param VARCHAR(250) NOT NULL, value VARCHAR(250) NOT NULL)")
            ->execute();

        $migrateVersion = $select
            ->query("SELECT value FROM {$this->table} WHERE param = 'migration_version'")
            ->fetchRow();

        if (isset($migrateVersion['value'])) {
            echo Utils::colorize("Migration tool is already initialized", "NOTE");
        } else {
            $select
                ->query("INSERT into {$this->table} (param, value) VALUES ('migration_version', 0)")
                ->execute();
            echo Utils::colorize("Initialization successful.", "SUCCESS");
        }
    }


    /**
     * Get current version of database
     */
    private function getVersion()
    {
        $select = new QueryBuilder();
        $versionRow = $select
            ->query("SELECT value FROM {$this->table} WHERE param = 'migration_version'")
            ->fetchRow();

        if (!isset($versionRow['value'])) die("Error: Please init the Crater Migration");
        return $versionRow['value'];
    }


    /**
     * Set version of database
     * @param string $version Version number
     * @return bool
     */
    private function setVersion($version)
    {
        $select = new QueryBuilder();
        $select
            ->query("UPDATE {$this->table} SET value = {$version} WHERE param = 'migration_version'")
            ->execute();
        return true;
    }


    /**
     * Get all migration files
     * @return array
     * @throws Exception
     */
    private function getMigrationsFiles()
    {
        $migrationPath = $this->migrateManager->storagePath;
        $files = array();
        if ($handle = opendir($migrationPath)) {

            while (false !== ($entry = readdir($handle))) {

                if ($entry != "." && $entry != ".." && is_file($migrationPath . '/' . $entry)) {
                    $segments = explode('_', $entry);
                    $files[$segments[0]] = $entry;
                }
            }

            closedir($handle);

            return $files;
        }

        die (Utils::colorize("Error to open migrations storage", "FAILURE"));
    }


    /**
     * Install all the migration files
     * @throws Exception
     */
    public function apply()
    {

        $files = $this->getMigrationsFiles();
        $lastMigration = null;

        asort($files);

        foreach ($files as $key => $value) {
            if ($key <= $this->getVersion()) continue;
            include $this->migrateManager->storagePath . '/' . $value;
            $className = "Migration_$key";
            $class = new $className();
            $class->up();
            $lastMigration = $key;
            unset($class);
        }

        if (!is_null($lastMigration)) {
            $this->setVersion($lastMigration);
            echo Utils::colorize("Done!", "SUCCESS");
        } else {
            echo Utils::colorize("The latest version is already installed.", "NOTE");
        }

    }


    /**
     * Return to a specific version
     * @param string $version Version number
     * @throws Exception
     */
    public function rollback($version)
    {
        $files = $this->getMigrationsFiles();
        $lastMigration = null;

        arsort($files);

        foreach ($files as $key => $value) {
            if ($key < $version) continue;
            include $this->migrateManager->storagePath . '/' . $value;
            $className = "Migration_$key";
            $class = new $className();
            $class->down();
            $lastMigration = $key;
            unset($class);
        }

        if (!is_null($lastMigration)) {
            $this->setVersion($lastMigration - 1);
            echo Utils::colorize("Done!", "SUCCESS");
        } else {
            echo Utils::colorize("The latest version is already installed.", "NOTE");
        }
    }
}