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

// Core Config
require APP_DIR . '/configs/core.php';

// Require Session
require CONFIGS_DIR . '/session.php';

// Basics Functions
require LIB_DIR . '/basic.php';

// Use Database Manager
use \jFramework\Core\DatabaseManager;

// Use Tools
use \jFramework\Core\Tools;

// Use View
use \jFramework\Core\View;

// Fix folders
tools::folder ( SESSION_DIR );

// Start Session on Dispatcher
session_start ();

// Signature
header('X-Powered-By: jFramework ' . VERSION, true);

// Setting content Type and Charset
header('Content-Type: text/html; charset=' . CHARSET, true);

// Set User IP
define('CLIENT_IP', $_SERVER ['REMOTE_ADDR']);

// Database config
require CONFIGS_DIR . '/database.php';


// Check if the controller is private
if (substr(CONTROLLER, - 1) == '_') {
    Tools::error(406);
}

// Creating new database manager OBJ
$db = new DatabaseManager ();

// Setting database settings
$db->setSettings($CONFIGS ['database']);

// Connecting to mysql database
$db->connect();

// Remove empty keys from get data
$_GET = array_filter($_GET);

// Require Controller
if (file_exists(CONTROLLERS_DIR . '/' . CONTROLLER . '.php')) {
    // Require Controller
    require CONTROLLERS_DIR . '/' . CONTROLLER . '.php';

    // Defining Render Options
    define('LAYOUT_PAGE', isset($layout) ? $layout : LAYOUT_DEFAULT );
    define('VIEW_PAGE', isset($view) ? $view : CONTROLLER );

    // Check out View
    $contents = View::renderView(VIEW_PAGE);
} else {
    // Generate a custom error page
    Tools::error(404);
}

// Start Tidy Cache Getter
if (class_exists('tidy') && $contents != '' && LAYOUT_PAGE == LAYOUT_DEFAULT) {
    ob_start();
}

// Request Layout controller
require CONTROLLERS_DIR . '/_layouts/' . LAYOUT_PAGE . '.php';

// Closing MySQL database connectiong
$db->close();

// Parse XHTML
if (class_exists('tidy') && $contents != '' && LAYOUT_PAGE == LAYOUT_DEFAULT) {
    $tidy = new tidy ();
    $tidy->parseString(ob_get_clean(), array('indent' => true, 'output-xhtml' => true, 'wrap' => 200), 'utf8');
    $tidy->cleanRepair();

    // Output
    echo $tidy;
}
