<?php
/**
 * Cli Build Class
 *
 * @author Dragos Ionita
 * @version 1.0
 * @date 3/24/15
 */

namespace Core\Cli;

use Core\Cli\Utils;

class Build
{

    /**
     * @var array $arguments Command line arguments
     */
    protected $arguments;


    /**
     * @var string $appPath Application full path
     */
    private $appPath;

    public function __construct($arguments)
    {
        $this->appPath = dirname(dirname(dirname(dirname(__DIR__)))) . '/App';

        array_shift($arguments);
        $this->arguments = $arguments;

        if (count($this->arguments) == 0) Screen::buildMain();

        if ($this->arguments[0] == 'controller') {
            if (isset($this->arguments[1])) {
                $this->controller($this->arguments[1]);
            } else {
                Screen::controllerNoName();
            }
        }

        if ($this->arguments[0] == 'model') {
            if (isset($this->arguments[1]) && isset($this->arguments[2])) {
                $this->model($this->arguments[1], $this->arguments[2]);
            } else {
                Screen::modelNoName();
            }
        }

        if ($this->arguments[0] == 'view') {
            if (isset($this->arguments[1])) {
                $this->view($this->arguments[1]);
            } else {
                Screen::viewNoName();
            }
        }

        if ($this->arguments[0] == 'template') {
            if (isset($this->arguments[1])) {
                $this->template($this->arguments[1]);
            } else {
                Screen::templateNoName();
            }
        }
    }


    /**
     * Generate view file
     * @param string $name Path and name of the view file
     * @return bool
     * @throws Exception
     */
    public function view($name)
    {
        $fullPath = $name . ".phtml";
        $directory = dirname($fullPath);

        $globalDirPath = $this->appPath . "/Views/" . $directory;
        $fileName = $globalDirPath . "/" . basename($fullPath);

        if (!is_dir($globalDirPath)) {
            // Dir doesn't exist, make it
            mkdir($globalDirPath, 0777, true);
        }

        if (file_put_contents($fileName, "<!-- Created by CLI -->")) {
            echo Utils::colorize('Create ' . $fileName, 'SUCCESS');

            return true;
        } else {
            echo Utils::colorize('Can\'t create ' . $fileName, 'FAILURE');

            return false;
        }
    }


    /**
     * Generate template file
     * @param string $name Path and name of the template file
     * @return bool
     * @throws Exception
     */
    public function template($name)
    {
        $fullPath = $name . ".phtml";
        $directory = dirname($fullPath);

        $globalDirPath = $this->appPath . "/Templates/" . $directory;
        $fileName = $globalDirPath . "/" . basename($fullPath);

        if (!is_dir($globalDirPath)) {
            // Dir doesn't exist, make it
            mkdir($globalDirPath, 0777, true);
        }

        $contents = array('<!DOCTYPE html>');
        $contents[] = '<html>';
        $contents[] = '    <head>';
        $contents[] = '        <meta charset="utf-8"/>';
        $contents[] = '        <title></title>';
        $contents[] = '        <script type="text/javascript" src=""></script>';
        $contents[] = '        <link rel="stylesheet" type="text/css" href="">';
        $contents[] = '    </head>';
        $contents[] = '    <body>';
        $contents[] = '        <?php $this->content()?>';
        $contents[] = '    </body>';
        $contents[] = '</html>';

        if (file_put_contents($fileName, implode("\n", $contents))) {
            echo Utils::colorize('Create ' . $fileName, 'SUCCESS');

            return true;
        } else {
            echo Utils::colorize('Can\'t create ' . $fileName, 'FAILURE');

            return false;
        }
    }


    /**
     * Generate new controller with actions
     * @param string $name Name of the controller
     * @return bool
     * @throws Exception
     */
    public function controller($name)
    {
        $fileName = "{$name}.php";

        if (isset($this->arguments[2])) {
            $actions = explode(',', $this->arguments[2]);

            if (count($actions) == 0) {
                $actions = array('index');
            }

        } else {
            $actions = array('index');
        }

        $file = $this->appPath . "/Controllers/" . $fileName;
        $class = $name;
        $date = date("n/j/Y");
        $contents = array('<?php');
        $contents[] = '/*';
        $contents[] = " * {$name} controller";
        $contents[] = ' *';
        $contents[] = ' * @author ';
        $contents[] = ' * @version 1.0';
        $contents[] = " * @date $date";
        $contents[] = ' */';
        $contents[] = '';
        $contents[] = 'namespace Controllers;';
        $contents[] = 'use \Core\Controller;';
        $contents[] = '';
        $contents[] = 'class ' . $class . ' extends Controller';
        $contents[] = '{';

        foreach ($actions as $action) {
            $contents[] = "";
            $contents[] = "    public function " . $action . "Action()";
            $contents[] = "    {";
            $contents[] = "        ";
            $contents[] = "    }";
            $contents[] = "";
        }

        $contents[] = '}';

        if (file_put_contents($file, implode("\n", $contents))) {
            echo Utils::colorize('Create ' . $file, 'SUCCESS');

            return true;
        } else {
            echo Utils::colorize('Can\'t create ' . $file, 'FAILURE');

            return false;
        }
    }


    /**
     * Generate new model
     * @param string $name Name of the model
     * @param string $primaryKey Name of the primary key column
     * @return bool
     * @throws Exception
     */
    public function model($name, $primaryKey)
    {
        $fileName = "{$name}.php";
        $tableName = strtolower($name);

        $class = $name;
        $date = date("n/j/Y");

        $fileTable = $this->appPath . "/Models/Table/" . $fileName;
        $fileRow = $this->appPath . "/Models/Row/" . $fileName;

        $contentsT = array('<?php');
        $contentsT[] = '/*';
        $contentsT[] = " * {$name} table model";
        $contentsT[] = ' *';
        $contentsT[] = ' * @author ';
        $contentsT[] = ' * @version 1.0';
        $contentsT[] = " * @date $date";
        $contentsT[] = ' */';
        $contentsT[] = '';
        $contentsT[] = 'namespace Models\Table;';
        $contentsT[] = 'use Core\Orm\TableGateway;';
        $contentsT[] = '';
        $contentsT[] = 'class ' . $class . ' extends TableGateway';
        $contentsT[] = "{";
        $contentsT[] = "    protected \$_name = '{$tableName}';";
        $contentsT[] = "    protected \$_primary = '{$primaryKey}';";
        $contentsT[] = "    protected \$_rowClass = 'Models\Row\$class';";
        $contentsT[] = "";
        $contentsT[] = '}';

        if (file_put_contents($fileTable, implode("\n", $contentsT))) {
            echo Utils::colorize('Create ' . $fileTable, 'SUCCESS');
        } else {
            echo Utils::colorize('Can\'t create ' . $fileTable, 'FAILURE');
        }

        $contentsR = array('<?php');
        $contentsR[] = '/*';
        $contentsR[] = " * {$name} row model";
        $contentsR[] = ' *';
        $contentsR[] = ' * @author ';
        $contentsR[] = ' * @version 1.0';
        $contentsR[] = " * @date $date";
        $contentsR[] = ' */';
        $contentsR[] = '';
        $contentsR[] = 'namespace Models\Row;';
        $contentsR[] = 'use Core\Orm\RowGateway;';
        $contentsR[] = '';
        $contentsR[] = 'class ' . $class . ' extends RowGateway';
        $contentsR[] = "{";
        $contentsR[] = "";
        $contentsR[] = '}';

        if (file_put_contents($fileRow, implode("\n", $contentsR))) {
            echo Utils::colorize('Create ' . $fileRow, 'SUCCESS');

            return true;
        } else {
            echo Utils::colorize('Can\'t create ' . $fileRow, 'FAILURE');

            return false;
        }
    }
}