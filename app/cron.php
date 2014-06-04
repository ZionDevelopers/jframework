<?php

/**
 * jFramework
 * 
 * @version 1.2.1
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */
if (defined('STDIN')) {
    $BASE_DIR = str_replace("\\", '/', dirname($_SERVER ['SCRIPT_NAME']));
    define('APP_DIR', str_replace("\\", '/', dirname(getcwd()) . '/app'));
    define('WEBROOT_DIR', str_replace("\\", '/', getcwd()));

    if (isset($argv [0])) {
        // Define Controller
        define('CONTROLLER', $argv [1]);

        // Core Config
        require APP_DIR . '/configs/cron.php';

        // Basics Functions
        require LIB_DIR . '/basic.php';

        // Database config
        require CONFIGS_DIR . '/database.php';

        // Check if the controller is private
        if (substr(CONTROLLER, - 1) == '_') {
            tools::error(406);
        }

        // Creating new database manager OBJ
        $db = new databaseManager ();

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
            tools::error(404);
        }

        // Closing MySQL database connectiong
        $db->close();
    }
}
