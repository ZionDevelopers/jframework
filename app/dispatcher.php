<?php
/**
 * jFramework
 * 
 * @version 1.2.1
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

// Core Config
require APP_DIR . '/configs/core.php';

// Signature
header ( 'X-Powered-By: jFramework ' . VERSION, true );

// Setting content Type and Charset
header ( 'Content-Type: text/html; charset=' . CHARSET, true );

// Set User IP
define ( 'CLIENT_IP', $_SERVER ['REMOTE_ADDR'] );

// Basics Functions
require LIB_DIR . '/basic.php';

// Database config
require CONFIGS_DIR . '/database.php';

// Require Session
require CONFIGS_DIR . '/session.php';

// Check if the controller is private
if (substr ( CONTROLLER, - 1 ) == '_') {
	tools::error ( 406 );
}

// Creating new database manager OBJ
$db = new databaseManager ();

// Setting database settings
$db->setSettings ( $CONFIGS ['database'] );

// Connecting to mysql database
$db->connect ();

// Remove empty keys from get data
$_GET = array_filter ( $_GET );

// Require Controller
if (file_exists ( CONTROLLERS_DIR . '/' . CONTROLLER . '.php' )) {
	// Require Controller
	require CONTROLLERS_DIR . '/' . CONTROLLER . '.php';
	// Defining Render Options
	define ( 'LAYOUT_PAGE', isset ( $layout ) ? $layout : LAYOUT_DEFAULT );
	define ( 'VIEW_PAGE', isset ( $view ) ? $view : CONTROLLER );
	// Check out View
	$contents = view::renderView ( VIEW_PAGE );
} else {
	// Generate a custom error page
	tools::error ( 404 );
}

// Start Tidy Cache Getter
if (class_exists ( 'tidy' ) && $contents != '' && LAYOUT_PAGE == LAYOUT_DEFAULT) {
	ob_start ();
}

// Request Layout controller
require CONTROLLERS_DIR . '/_layouts/' . LAYOUT_PAGE . '.php';

// Closing MySQL database connectiong
$db->close ();

// Parse XHTML
if (class_exists ( 'tidy' ) && $contents != '' && LAYOUT_PAGE == LAYOUT_DEFAULT) {
	$tidy = new tidy ();
	$tidy->parseString ( ob_get_clean (), array ('indent' => true, 'output-xhtml' => true, 'wrap' => 200), 'utf8' );
	$tidy->cleanRepair ();
	
	// Output
	echo $tidy;
}
