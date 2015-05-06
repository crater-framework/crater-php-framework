<?php
/**
 * Cli Screen Class
 * Content all screens
 *
 * @author Dragos Ionita
 * @version 1.0
 * @date 3/24/15
 */

namespace Core\Cli;
include __DIR__ . '/Utils.php';
use Core\Cli\Utils;

class Screen
{
    static public function main()
    {
        $q = Utils::colorize("Crater PHP Framework CLI", "NOTE") .
            "Version 1.0\r\n" .
            "\r\n" .
            "Commands:\r\n" .
            "build              create controllers, models, views or templates\r\n" .
            "migrate            migrate your db\r\n" .
            "version            get version of Crater Framework\r\n";

        echo $q;
        exit;
    }

    static public function migrateMain()
    {
        $q = "Migration Commands:\r\n" .
            "\r\n" .
            "migrate init                       Init Crater Migration\r\n" .
            "migrate new [name]                 Create new migration file\r\n" .
            "migrate apply                      Apply all migration\r\n" .
            "migrate rollback [version]         Rollback version\r\n";

        echo $q;
        exit;
    }

    static public function rollbackNoVersion()
    {
        $q = Utils::colorize("Rollback Error", "WARNING") .
            "Please set the version to which you want to return\r\n" .
            "Example:\r\n" .
            "migrate rollback 1427256400\r\n";

        echo $q;
        exit;
    }

    static public function buildMain()
    {
        $q = "Builder Commands:\r\n" .
            "\r\n" .
            "build controller [name] [action1,action2...]   Create a new controller class\r\n" .
            "build model [name] [primary key]               Create a new model class\r\n" .
            "build view [name]                              Create a new view file\r\n" .
            "build template [name]                          Create a new template file\r\n";

        echo $q;
        exit;
    }

    static public function controllerNoName()
    {
        $q = Utils::colorize("Build Controller Error", "WARNING") .
            "Please set the name of controller\r\n" .
            "Example:\r\n" .
            "build controller Product index,view,list,delete\r\n";

        echo $q;
        exit;
    }

    static public function modelNoName()
    {
        $q = Utils::colorize("Build Model Error", "WARNING") .
            "Please set the name of model\r\n" .
            "Example:\r\n" .
            "build model User user_id\r\n";

        echo $q;
        exit;
    }

    static public function viewNoName()
    {
        $q = Utils::colorize("Build View Error", "WARNING") .
            "Please set the name of view file\r\n" .
            "Example:\r\n" .
            "build view user\\index \r\n";

        echo $q;
        exit;
    }

    static public function templateNoName()
    {
        $q = Utils::colorize("Build Template Error", "WARNING") .
            "Please set the name of template file\r\n" .
            "Example:\r\n" .
            "build template admin \r\n";

        echo $q;
        exit;
    }
}