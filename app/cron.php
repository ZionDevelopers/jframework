<?php
/**
 * jFramework
 *
 * @version 1.3.0
 * @link https://github.com/ZionDevelopers/jframework/ The jFramework GitHub Project
 * @copyright 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

$BASE_DIR = str_replace("\\", '/', dirname($_SERVER ['SCRIPT_NAME']));
define('APP_DIR', str_replace("\\", '/', dirname(getcwd()) . '/app'));
define('WEBROOT_DIR', str_replace("\\", '/', getcwd()));

// Core Config
require APP_DIR . '/configs/cron.php';

// Basics Functions
require LIB_DIR . '/basic.php';

// Use Database Manager
use \jFramework\Core\DatabaseManager;
// Use Tools
use \jFramework\Core\Tools;

if (defined('STDIN')) {
    if (isset($argv [0])) {
        // Define Controller
        define('CONTROLLER', $argv [1]);

        // Database config
        require CONFIGS_DIR . '/database.php';       

        // Creating new database manager OBJ
        $db = new DatabaseManager ();

        // Setting database settings
        $db->setSettings($CONFIGS ['database']);

        // Connecting to mysql database
        $db->connect();

        // Require Controller
        if (file_exists(CONTROLLERS_DIR . '/_cron/' . CONTROLLER . '.php')) {
            // Require Controller
            require CONTROLLERS_DIR . '/_cron/' . CONTROLLER . '.php';
        } else {
            // Generate a custom error page
            echo 'Cron controller \'', CONTROLLER, '\' not exists';
        }

        // Closing MySQL database connectiong
        $db->close();
    }
}
