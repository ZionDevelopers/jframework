<?php
// Signature
@header ( 'X-Powered-By: jFramework 1.1', true );

// Set User IP
define ( 'CLIENT_IP', $_SERVER ['REMOTE_ADDR'] );

// Requesting Configs and Library Classes
// Basics Functions
require (APP_DIR . '/library/basic.php');

// Core Config
require (APP_DIR . '/configs/core.php');

// Tools Library
require (LIBRARY_DIR . '/tools.php');

// Start Timer
$timer = tools::timer ( 1 );

// View Library
tools::Library ( 'view' );

// Setting content Type and Charset
header ( 'Content-Type: text/html; charset=' . CHARSET, true );

// Check if the controller is private
if (substr ( CONTROLLER, - 1 ) == '_') {
	tools::error ( 406 );
}

// Remove empty keys from get data
$_GET = array_filter ( $_GET );

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

// Request Layout controller
require (CONTROLLERS_DIR . '/_layouts/' . LAYOUT_PAGE . '.php');
?>