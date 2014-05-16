<?php
/**
 * jFramework
 * @copyright Copyright (c) 2010-2014, Júlio César de Oliveira
 * @author Júlio César de Oliveira <talk@juliocesar.me>
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2.0 License
 */

// Signature
@header ( 'X-Powered-By: jFramework ' . VERSION, true );

// Set User IP
define ( 'CLIENT_IP', $_SERVER ['REMOTE_ADDR'] );

// Requesting Configs and Library Classes
// Basics Functions
require (APP_DIR . '/library/basic.php');

// Core Config
require (APP_DIR . '/configs/core.php');

// Tools Library
require (LIBRARY_DIR . '/tools.php');
exit("ALAL");
// Load Database Config
tools::Configs ( 'database' );
// Load Mail Config
tools::Configs ( 'mail' );
// Load Session Config
tools::Configs ( 'session' );

// Load View Library
tools::Library ( 'view' );

// Load Database Manager Library
tools::Library ( 'databaseManager' );

// Create Database Object
$db = new DatabaseManager ();

// *********** Set Configs : START ***********//
// Set Email configs
tools::$mail_configs = $CONFIGS ['mail'];
// Set Database Configs
$db->setSettings ( $CONFIGS ['database'] );
// *********** Set Configs : END ***********//

// Setting content Type and Charset
header ( 'Content-Type: text/html; charset=' . CHARSET, true );

// Starting database connection
$db->connect ();

// SET to archive sql History like DEBUG (YES OR NOT)
$db->sqlArchive = DEBUG;

// Copy db object to tools
tools::$db = & $db;

// Check if the controller is private
if (substr ( CONTROLLER, - 1 ) == '_') {
	tools::error ( 406 );
}

// Remove empty keys from get data
$_GET = array_filter ( $_GET );

// Try Exception Handler
try {
	// Require Controller
	if (file_exists ( CONTROLLERS_DIR . '/' . CONTROLLER . '.php' )) {
		// Require Controller
		require (CONTROLLERS_DIR . '/' . CONTROLLER . '.php');
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
	require (CONTROLLERS_DIR . '/_layouts/' . LAYOUT_PAGE . '.php');
	
	// End Connection
	$db->close ();
	
	// Parse XHTML
	if (class_exists ( 'tidy' ) && $contents != '' && LAYOUT_PAGE == LAYOUT_DEFAULT) {
		$tidy = new tidy ();
		$tidy->parseString ( ob_get_clean (), array ( 'indent' => true, 'output-xhtml' => true, 'wrap' => 200 ), 'utf8' );
		$tidy->cleanRepair ();
		
		// Output
		echo $tidy;
	}
	
	// Exception Catcher
} catch ( Exception $e ) {
	// Debug
	tools::debug ( $e->getMessage () . '<br />' . tools::traceException ( $e ) );
}
?>